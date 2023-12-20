<?php

namespace App\Service;

use App\Repository\Vacation\Settings\BankHolidayRepository;
use DateTime;

class WorkingDaysCounterService
{
    public static function countWorkingDays(\DateTimeInterface $fromDate, \DateTimeInterface $toDate, BankHolidayRepository $bankHolidayRepository): int
    {

        $holidays = $bankHolidayRepository->findAll();

        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $workingDays = 0;

        $currentDate = clone $fromDate;
        while ($currentDate <= $toDate) {
            if (self::isWorkingDay($currentDate)) {
                if(!in_array($currentDate->format('Y-m-d'), $holidays)){
                    $workingDays++;
                }
            }
            $currentDate->modify('+1 day');
        }

        return $workingDays;
    }

    private static function isWorkingDay(\DateTimeInterface $date): bool
    {
        return $date->format('N') < 6;
    }
}