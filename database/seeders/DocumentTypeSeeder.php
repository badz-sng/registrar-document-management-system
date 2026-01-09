<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'F-137', 'processing_category' => 'transcript'],
            ['name' => 'F-138', 'processing_category' => 'transcript'],
            ['name' => 'TOR', 'processing_category' => 'transcript'],

            ['name' => 'Transfer Credential', 'processing_category' => 'ctc'],
            ['name' => 'Honorable Dismissal', 'processing_category' => 'ctc'],

            ['name' => 'Good Moral Certificate', 'processing_category' => 'certificate'],
            ['name' => 'Diploma', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Grades', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Enrollment', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Graduation', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Ranking', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Completion', 'processing_category' => 'certificate'],
            ['name' => 'Certificate of Latin Honors', 'processing_category' => 'certificate'],
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['name' => $type['name']],
                ['processing_category' => $type['processing_category']]
            );
        }
    }
}
