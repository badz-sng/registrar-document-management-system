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
            'student_name' => 'required|string|max:255',
            'document_type_name' => 'required|string|max:255',
        ]);

        // 🔹 Create or find the Student
        $student = Student::firstOrCreate(['name' => $validated['student_name']]);

        // 🔹 Create or find the Document Type
        $documentType = DocumentType::firstOrCreate(['name' => $validated['document_type_name']]);

        // 🔹 Determine processing days and estimated release date
        $processingDays = ProcessingDays::getDays($documentType->name);
        $releaseDate = ProcessingDays::computeReleaseDate(Carbon::now(), $processingDays);

        // 🔹 Create the Request
        RequestModel::create([
            'student_id' => $student->id,
            'document_type_id' => $documentType->id,
            'encoded_by' => Auth::id(),
            'status' => 'Pending',
            'encoded_at' => now(),
            'estimated_release_date' => $releaseDate,
        ]);

        return back()->with('success', 'Request successfully encoded.');
    }
}
