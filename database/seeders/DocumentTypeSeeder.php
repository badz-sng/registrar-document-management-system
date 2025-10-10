<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'F-137',
            'F-138',
            'TOR',
            'Transfer Credential',
            'Good Moral Certificate',
            'Diploma',
            'Certificate of Grades',
            'Certificate of Enrollment',
            'Certificate of Graduation',
            'Honorable Dismissal',
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(['name' => $type]);
        }
    }
}