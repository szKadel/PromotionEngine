<?php

namespace App\Service;

use App\Repository\Vacation\Settings\BankHolidayRepository;
use DateTimeImmutable;

class WorkingDaysCounterService
{
    public static function countWorkingDays(\DateTimeInterface $fromDate, \DateTimeInterface $toDate, BankHolidayRepository $bankHolidayRepository): int
    {
        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $workingDays = 0;

        $currentDate = clone $fromDate;
        while ($currentDate <= $toDate) {
            if (self::isWorkingDay($currentDate)) {
                $dateTimeImmutable = DateTimeImmutable::createFromFormat('Y-m-d', $currentDate->format('Y-m-d'));
                $holiday = $bankHolidayRepository->findOneBy(['date' => $dateTimeImmutable]);
                if($holiday == null) {
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