<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('representative_id')->nullable()->constrained('representatives')->onDelete('set null');
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('cascade');
            $table->foreignId('authorization_id')->nullable()->constrained('authorizations')->onDelete('set null');
            $table->enum('status', ['pending', 'in_process', 'ready_for_verification', 'verified', 'released'])->default('pending');
            $table->foreignId('encoded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('retriever_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('processor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verifier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
