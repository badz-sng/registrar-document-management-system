<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_no', 'name', 'course', 'year_level',
        'address', 'contact_number', 'email'
    ];

    public function requests()
    {
        return $this->hasMany(RequestModel::class);
    }

    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }

    public function envelope()
    {
        return $this->hasOne(Envelope::class);
    }
}

