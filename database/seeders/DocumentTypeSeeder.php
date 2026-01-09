<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'F-137', 'processing_category' => 'student'],
            ['name' => 'F-138', 'processing_category' => 'student'],
            ['name' => 'TOR', 'processing_category' => 'student'],
            ['name' => 'Transfer Credential', 'processing_category' => 'student'],
            ['name' => 'Good Moral Certificate', 'processing_category' => 'student'],
            ['name' => 'Diploma', 'processing_category' => 'student'],
            ['name' => 'Certificate of Grades', 'processing_category' => 'student'],
            ['name' => 'Certificate of Enrollment', 'processing_category' => 'student'],
            ['name' => 'Certificate of Graduation', 'processing_category' => 'student'],
            ['name' => 'Honorable Dismissal', 'processing_category' => 'student'],
            ['name' => 'Certificate of Ranking', 'processing_category' => 'student'],
            ['name' => 'Certificate of Completion', 'processing_category' => 'student'],
            ['name' => 'Certificate of Latin Honors', 'processing_category' => 'student'],
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['name' => $type['name']],
                ['processing_category' => $type['processing_category']]
            );
        }
    }
}
