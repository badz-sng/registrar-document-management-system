<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Envelope extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'storage_location', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
