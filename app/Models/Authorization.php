<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'representative_id',
        'authorization_letter_path', 'valid_until'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}

