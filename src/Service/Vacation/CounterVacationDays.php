<?php

namespace App\Service\Vacation;

use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationTypes;
use App\Repository\Vacation\Settings\BankHolidayRepository;
use App\Repository\Vacation\VacationRepository;
use App\Repository\Vacation\VacationTypesRepository;

class CounterVacationDays
{
    const HOLIDAY = "Urlop Wypoczynkowy";
    const HOLIDAY_ON_DEMAND = "Urlop na żądanie";

    public function __construct(
        private readonly VacationRepository $vacationRepository,
        private readonly VacationTypesRepository $typesRepository,
        private readonly BankHolidayRepository $bankHolidayRepository
    ) {
    }

    public function countHolidaysForEmployee(Employee $employee, int $year = null): int
    {
        $holidayType = $this->typesRepository->findBy(['name'=>self::HOLIDAY])[0];
        $holidayOnRequestType = $this->typesRepository->findBy(['name'=>self::HOLIDAY_ON_DEMAND])[0];

        $spendDaysOnRequestType = $this->countVacationSpendDays($employee, $holidayOnRequestType,$year);
        $spendDaysStandardVacation = $this->countVacationSpendDays($employee, $holidayType,$year);

        return $spendDaysStandardVacation + $spendDaysOnRequestType;
    }

    public function countVacationSpendDays(Employee $employee, VacationTypes $vacationType, int $year = null) :int
    {
        $days = 0;

        if($year != null){
            $this->vacationRepository->setYear($year);
        }

        if(!empty($result = $this->vacationRepository->findVacationUsedByUserArray($employee, $vacationType))) {
            foreach ($result as $element) {
                if($element instanceof Vacation) {
                    $days +=  WorkingDaysCounterService::countWorkingDays($element->getDateFrom(),$element->getDateTo(),$this->bankHolidayRepository);
                }
            }
        }

        return $days;

    }

}