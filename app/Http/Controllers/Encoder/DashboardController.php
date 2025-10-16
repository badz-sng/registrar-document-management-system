<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\Student;
use App\Models\DocumentType;
use App\Helpers\ProcessingDays;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('encoded_by', auth()->id())->get();

        // you might also want to pass $students and $documentTypes if needed
        $students = Student::all();
        $documentTypes = DocumentType::all();

        return view('encoder.dashboard', compact('requests', 'students', 'documentTypes'));
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_school_year' => 'nullable|string|max:50',
            'document_type_id' => 'nullable|array',
            'document_type_id.*' => 'nullable',
            'document_type_other' => 'nullable|string|max:255',
            'is_representative' => 'nullable|boolean',
            'representative_name' => 'nullable|string|max:255',
        ]);

        // Build full name for Student (legacy models expect single 'name' column)
        $fullName = trim($validated['last_name'] . ', ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));

        // Create or find the Student (include last_school_year if provided)
        $studentData = ['name' => $fullName];
        // Ensure student_no is present (DB currently requires it). Use empty string if not provided.
        $studentData['student_no'] = $request->input('student_no', '');
        if ($request->filled('last_school_year')) {
            $studentData['last_school_year'] = $request->input('last_school_year');
        }
        $student = Student::firstOrCreate($studentData);

        // Determine document type ids
        $selected = $request->input('document_type_id', []);
        $docIds = [];
        if (is_array($selected)) {
            foreach ($selected as $sel) {
                if ($sel === 'other') continue; // handled separately
                // cast to int if numeric
                if (is_numeric($sel)) $docIds[] = (int)$sel;
            }
        }

        // If an 'other' type was provided, create/find it
        if (in_array('other', (array)$selected) && $request->filled('document_type_other')) {
            $other = DocumentType::firstOrCreate(['name' => $request->input('document_type_other')]);
            $docIds[] = $other->id;
        }

        if (empty($docIds)) {
            return back()->withErrors(['document_type_id' => 'Please select at least one document type'])->withInput();
        }

        // Use the first doc id as the legacy document_type_id for compatibility
        $firstDocId = $docIds[0];

        // Determine representative name
        $repName = null;
        if ($request->has('is_representative') && $request->boolean('is_representative')) {
            $repName = $request->input('representative_name');
        }

        // Determine processing days based on the document types (use max)
        $processingDaysList = [];
        foreach ($docIds as $docId) {
            $doc = DocumentType::findOrFail($docId);
            $processingDaysList[] = ProcessingDays::getDays($doc->name);
        }
        $maxProcessingDays = max($processingDaysList);
        $releaseDate = ProcessingDays::computeReleaseDate(Carbon::now(), $maxProcessingDays);

        // Create the Request
        RequestModel::create([
            'student_id' => $student->id,
            'document_type_id' => $firstDocId,
            'document_type_ids' => $docIds,
            'representative_name' => $repName,
            'encoded_by' => Auth::id(),
            'status' => 'Pending',
            'encoded_at' => now(),
            'estimated_release_date' => $releaseDate,
        ]);

        return back()->with('success', 'Request successfully encoded.');
    }
}
