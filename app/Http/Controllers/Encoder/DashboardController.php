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

use App\Mail\RequestEncodedConfirmation;
use Illuminate\Support\Facades\Mail;

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
            'student_no' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'document_type_id' => 'nullable|array',
            'document_type_id.*' => 'nullable',
            'document_type_other' => 'nullable|string|max:255',
            'is_representative' => 'nullable|boolean',
            'representative_name' => 'nullable|string|max:255',
        ]);

        // Build full name for Student (legacy models expect single 'name' column)
        $fullName = trim($validated['last_name'] . ', ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));

        // Create or find the Student (include all fields)
        $studentData = ['name' => $fullName];
        $studentData['student_no'] = $validated['student_no'] ?? '';
        if ($validated['course'] ?? null) $studentData['course'] = $validated['course'];
        if ($validated['year_level'] ?? null) $studentData['year_level'] = $validated['year_level'];
        if ($validated['address'] ?? null) $studentData['address'] = $validated['address'];
        if ($validated['contact_number'] ?? null) $studentData['contact_number'] = $validated['contact_number'];
        if ($validated['email'] ?? null) $studentData['email'] = $validated['email'];
        if ($validated['last_school_year'] ?? null) $studentData['last_school_year'] = $validated['last_school_year'];
        
        $student = Student::firstOrCreate(['name' => $fullName], $studentData);

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

        // Create the Request
        $requestModel = RequestModel::create([
            'student_id' => $student->id,
            'document_type_id' => $firstDocId,
            'document_type_ids' => $docIds,
            'representative_name' => $repName,
            'encoded_by' => Auth::id(),
            'status' => 'Pending',
            'encoded_at' => now(),
        ]);

        // Send confirmation email if student has email
        \Log::info('Attempting to send confirmation email', [
            'student_id' => $student->id,
            'student_email' => $student->email,
            'has_email' => !empty($student->email),
            'request_id' => $requestModel->id,
        ]);

        if ($student->email) {
            try {
                Mail::to($student->email)->send(new RequestEncodedConfirmation($requestModel));
                \Log::info('Confirmation email sent successfully', [
                    'to' => $student->email,
                    'request_id' => $requestModel->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send confirmation email', [
                    'error' => $e->getMessage(),
                    'to' => $student->email,
                    'request_id' => $requestModel->id,
                ]);
            }
        } else {
            \Log::warning('No email address found for student', [
                'student_id' => $student->id,
                'request_id' => $requestModel->id,
            ]);
        }

        return back()->with('success', 'Request successfully encoded.');
    }
}
