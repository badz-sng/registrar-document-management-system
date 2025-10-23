<?php

namespace Tests\Feature;

use App\Models\RequestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetrieverCanMarkRetrievedTest extends TestCase
{
    use RefreshDatabase;

    public function test_retriever_can_mark_pending_request_as_retrieved()
    {
        // Create a retriever user and a pending request with no retriever assigned
        $retriever = User::factory()->create(['role' => User::ROLE_RETRIEVER]);

        // Create required related records: a student, a document type and an encoder
        $student = \App\Models\Student::create([
            'student_no' => 'S-TEST-001',
            'name' => 'Test Student',
            'course' => 'Test Course',
            'year_level' => '1',
        ]);
        $docType = \App\Models\DocumentType::create([
            'name' => 'Test Doc',
            'processing_category' => 'certificate',
        ]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER]);

        $requestModel = RequestModel::create([
            'student_id' => $student->id,
            'document_type_id' => $docType->id,
            'status' => 'pending',
            'encoded_by' => $encoder->id,
        ]);

        $response = $this->actingAs($retriever)->post(route('retriever.update.status', $requestModel->id));

        $response->assertRedirect();

        $requestModel->refresh();

        $this->assertEquals('retrieved', $requestModel->status);
        $this->assertEquals($retriever->id, $requestModel->retriever_id);
    }
}
