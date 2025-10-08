<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\Student;
use App\Models\DocumentType;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function index()
    {
        $requests = RequestModel::with(['student', 'documentType', 'encoder'])->latest()->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $students = Student::all();
        $documentTypes = DocumentType::all();
        return view('requests.create', compact('students', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'document_type_id' => 'required|exists:document_types,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'authorization_id' => 'nullable|exists:authorizations,id',
        ]);

        $data['encoded_by'] = auth()->id();
        $data['status'] = 'Pending';

        // 🗓 Get the document type to determine processing days
        $documentType = DocumentType::findOrFail($data['document_type_id']);
        $processingDays = $this->getProcessingDays($documentType->name);

        // 📅 Calculate estimated release date excluding weekends & holidays
        $data['encoded_at'] = now();
        $data['estimated_release_date'] = $this->calculateReleaseDate(now(), $processingDays);

        RequestModel::create($data);

        return redirect()->route('requests.index')->with('success', 'Request recorded successfully!');
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
