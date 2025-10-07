<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingLog extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'personnel_id', 'action'];

    public function request()
    {
        return $this->belongsTo(RequestModel::class);
    }

    public function personnel()
    {
        return $this->belongsTo(User::class, 'personnel_id');
    }
}

