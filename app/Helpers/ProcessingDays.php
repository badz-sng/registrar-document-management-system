<?php

namespace App\Helpers;

use Carbon\Carbon;
// This function computes the processing days for different document types
// para hindi na kailangang manual na lagyan ng days sa bawat document type
// and also computes the release date excluding weekends and holidays :)
class ProcessingDays
{
    public static function getProcessingDays($documentType)
    {
        return match ($documentType) {
            'F-137' => 10,
            'F-138' => 5,
            'TOR' => 10,
            'Transfer Credential' => 4,
            'Good Moral Certificate' => 7,
            'Diploma' => 7,
            'Certificate of Grades' => 10,
            'Certificate of Enrollment' => 4,
            'Certificate of Graduation' => 4,
            'Honorable Dismissal' => 4,
            default => 5,
        };
    }

    public static function computeReleaseDate($startDate, $documentType)
    {
        $days = self::getProcessingDays($documentType);
        $date = Carbon::parse($startDate);

        while ($days > 0) {
            $date->addDay();
            if ($date->isWeekend() || self::isHoliday($date)) continue;
            $days--;
        }
        return $date;
    }

    private static function isHoliday($date)
    {
        $holidays = [
            '2025-01-01', '2025-06-12', '2025-11-01', '2025-12-25', '2025-12-30'
        ];
        return in_array($date->format('Y-m-d'), $holidays);
    }
}
