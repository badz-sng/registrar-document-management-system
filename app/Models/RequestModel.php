<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestModel extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'student_id',
        'representative_id',
        'document_type_id',
        'document_type_ids',
        'representative_name',
        'authorization_id',
        'status',
        'encoded_by',
        'retriever_id',
        'processor_id',
        'verifier_id',
        'verified_at',
        'encoded_at',
        'estimated_release_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Casts
     * - document_type_ids stored as JSON in DB, cast to array in PHP
     */
    protected $casts = [
        'document_type_ids' => 'array',
        'estimated_release_date' => 'datetime',
        'encoded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Helper to get DocumentType models for this request.
     * Returns a Collection of DocumentType instances based on document_type_ids (if present)
     * Falls back to document_type_id when document_type_ids is empty for backwards compatibility.
     */
    public function documentTypes()
    {
        $ids = $this->document_type_ids ?? [];
        if (empty($ids) && $this->document_type_id) {
            $ids = [$this->document_type_id];
        }

        return \App\Models\DocumentType::whereIn('id', $ids)->get();
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function authorization()
    {
        return $this->belongsTo(Authorization::class);
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function retriever()
    {
        return $this->belongsTo(User::class, 'retriever_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processor_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function processingLogs()
    {
        return $this->hasMany(ProcessingLog::class);
    }

    public function releaseRecord()
    {
        return $this->hasOne(ReleaseRecord::class);
    }
}

