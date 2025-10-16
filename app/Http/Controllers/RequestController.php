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
            'student_no' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year_level' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'document_type_id' => 'required|array',
            'document_type_id.*' => 'required|exists:document_types,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'authorization_id' => 'nullable|exists:authorizations,id',
        ]);

        // Create or find the student by all fillable fields
        $student = Student::firstOrCreate([
            'student_no' => $validated['student_no'],
            'name' => $validated['name'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
        ]);

        // Get all selected document types
        $documentTypeIds = $validated['document_type_id'];
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
            // keep legacy column populated with first selected id for DB compatibility
            'document_type_id' => count($documentTypeIds) ? $documentTypeIds[0] : null,
            'document_type_ids' => $documentTypeIds,
            'representative_id' => $validated['representative_id'] ?? null,
            'authorization_id' => $validated['authorization_id'] ?? null,
            'encoded_by' => auth()->id(),
            'status' => 'Pending',
            'encoded_at' => now(),
            'estimated_release_date' => $releaseDate,
        ];

        RequestModel::create($data);

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
}
