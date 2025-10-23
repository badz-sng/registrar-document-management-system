<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use App\Models\Student;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RequestController extends Controller
{
    public function index()
    {
        // Get all requests encoded by the logged-in user
        $requests = RequestModel::where('encoded_by', auth()->id())->latest()->get();

        // Get data for dropdowns
        $students = Student::all();
        $documentTypes = DocumentType::all();

        // Pass everything to the view
        return view('encoder.dashboard', compact('requests', 'students', 'documentTypes'));
    }

    public function create()
    {
        $students = Student::all();
        $documentTypes = DocumentType::all();
        return view('requests.create', compact('students', 'documentTypes'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            // Accept either legacy fields or new split name fields
            'student_no' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_school_year' => 'nullable|string|max:50',
            'course' => 'required|string|max:255',
            'year_level' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'document_type_id' => 'required|array',
            'document_type_id.*' => 'required',
            'document_type_other' => 'nullable|string|max:255',
            'representative_id' => 'nullable|exists:representatives,id',
            'representative_name' => 'nullable|string|max:255',
            'authorization_id' => 'nullable|exists:authorizations,id',
        ]);

        // Build student name: prefer split fields if provided, else fallback to legacy name
        if (!empty($validated['last_name']) && !empty($validated['first_name'])) {
            $studentName = trim($validated['last_name'] . ', ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));
        } else {
            $studentName = $validated['name'] ?? null;
        }

        // Create or find the student by provided fields
        $studentData = [
            'name' => $studentName,
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            // DB requires student_no; default to empty string if not provided
            'student_no' => $validated['student_no'] ?? '',
        ];
        if (!empty($validated['last_school_year'])) {
            $studentData['last_school_year'] = $validated['last_school_year'];
        }

        $student = Student::firstOrCreate($studentData);

        // Get all selected document types (handle 'other' option)
        $selected = $request->input('document_type_id', []);
        $documentTypeIds = [];
        foreach ((array)$selected as $sel) {
            if ($sel === 'other') continue;
            if (is_numeric($sel)) $documentTypeIds[] = (int)$sel;
        }
        if (in_array('other', (array)$selected) && $request->filled('document_type_other')) {
            $other = DocumentType::firstOrCreate(['name' => $request->input('document_type_other')]);
            $documentTypeIds[] = $other->id;
        }

        if (empty($documentTypeIds)) {
            return back()->withErrors(['document_type_id' => 'Please select at least one document type'])->withInput();
        }
        $processingDaysList = [];
        foreach ($documentTypeIds as $docTypeId) {
            $docType = DocumentType::findOrFail($docTypeId);
            $processingDaysList[] = $this->getProcessingDays($docType->name);
        }
        $maxProcessingDays = max($processingDaysList);
        $releaseDate = $this->calculateReleaseDate(now(), $maxProcessingDays);

        // Cutoff logic: if total requests for any selected document type exceeds 10 for the release date, add 1 day for all
        $cutoffExceeded = false;
        foreach ($documentTypeIds as $docTypeId) {
            // Count legacy rows with single document_type_id
            $countLegacy = RequestModel::where('document_type_id', $docTypeId)
                ->whereDate('estimated_release_date', $releaseDate->format('Y-m-d'))
                ->count();

            // Count new rows where document_type_ids (JSON array) contains this id
            $countJson = RequestModel::whereJsonContains('document_type_ids', $docTypeId)
                ->whereDate('estimated_release_date', $releaseDate->format('Y-m-d'))
                ->count();

            $count = $countLegacy + $countJson;

            if ($count >= 10) {
                $cutoffExceeded = true;
                break;
            }
        }
        if ($cutoffExceeded) {
            // Add 1 day, skipping weekends/holidays
            $holidays = [
                '2025-01-01', '2025-04-17', '2025-04-18', '2025-06-12', '2025-11-01', '2025-12-25', '2025-12-30'
            ];
            do {
                $releaseDate->addDay();
            } while ($releaseDate->isWeekend() || in_array($releaseDate->format('Y-m-d'), $holidays));
        }

        // Create a single Request row that stores all selected document type ids.
        // For backwards compatibility we leave document_type_id NULL and populate document_type_ids JSON.
        $data = [
            'student_id' => $student->id,
            'document_type_id' => count($documentTypeIds) ? $documentTypeIds[0] : null,
            'document_type_ids' => $documentTypeIds,
            'representative_id' => $validated['representative_id'] ?? null,
            'representative_name' => $validated['representative_name'] ?? null,
            'authorization_id' => $validated['authorization_id'] ?? null,
            'encoded_by' => auth()->id(),
            'status' => 'Pending',
            'encoded_at' => now(),
            'estimated_release_date' => $releaseDate,
        ];

        $requestModel = RequestModel::create($data);
        $requestModel->documentTypes()->attach($documentTypeIds);

        return redirect()->route('requests.index')->with('success', 'Requests recorded successfully!');
    }

    public function show(RequestModel $request)
    { 
        return view('requests.show', compact('request'));
    }

    /**
     * Determine processing days based on document type.
     */
    private function getProcessingDays($documentName)
    {
        return match (strtolower($documentName)) {
            'f-137' => 10,
            'f-138' => 5,
            'tor' => 10,
            'transfer credential' => 4,
            'good moral certificate' => 7,
            'diploma' => 7,
            'certificate of grades' => 10,
            'certificate of enrollment' => 4,
            'certificate of graduation' => 4,
            'honorable dismissal' => 4,
            default => 5, // fallback default
        };
    }

    /**
     * Calculate release date excluding weekends & PH holidays.
     */
    private function calculateReleaseDate(Carbon $startDate, int $daysNeeded)
    {
        $holidays = [
            '2025-01-01', // New Year
            '2025-04-17', // Maundy Thursday
            '2025-04-18', // Good Friday
            '2025-06-12', // Independence Day
            '2025-11-01', // All Saints’ Day
            '2025-12-25', // Christmas Day
            '2025-12-30', // Rizal Day
        ];

        $date = $startDate->copy();
        $addedDays = 0;

        while ($addedDays < $daysNeeded) {
            $date->addDay();

            // Skip weekends & holidays
            if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }

            $addedDays++;
        }

        return $date;
    }

    public function togglePrepared(RequestModel $request, DocumentType $document)
    {
    // Ensure pivot relation exists
    $pivotRecord = $request->documentTypes()->where('document_type_id', $document->id)->first();

    if (!$pivotRecord || !$pivotRecord->pivot) {
        return back()->with('error', 'Pivot record not found.');
    }

    // Flip the is_prepared field
    $isPrepared = !$pivotRecord->pivot->is_prepared;
    $pivotRecord->pivot->is_prepared = $isPrepared;
    $pivotRecord->pivot->save();

    // Check if all documents for this request are prepared
    $allPrepared = $request->documentTypes()->wherePivot('is_prepared', false)->count() === 0;

    // Update the main request status if all prepared
    if ($allPrepared && $request->status !== 'ready_for_verification') {
        $request->update(['status' => 'ready_for_verification']);
    }

    return back()->with('success', 'Document preparation status updated!');
    }

}
