<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReleaseRecord extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'released_by', 'released_to', 'release_date'];

    public function request()
    {
        return $this->belongsTo(RequestModel::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }
}

