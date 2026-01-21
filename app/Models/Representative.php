<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Representative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'relationship_to_student', 'address',
        'contact_number', 'email', 'valid_id_path'
    ];

    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }

    public function requests()
    {
        return $this->hasMany(RequestModel::class);
    }
}

