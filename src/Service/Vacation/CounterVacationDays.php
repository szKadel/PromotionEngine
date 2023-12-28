<?php

namespace App\Service\Vacation;

use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationStatus;
use App\Entity\Vacation\VacationTypes;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\Vacation\Settings\BankHolidayRepository;
use App\Repository\VacationRepository;
use App\Repository\VacationTypesRepository;
use App\Service\WorkingDaysCounterService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CounterVacationDays
{
    const HOLLIDAY = "Urlop Wypoczynkowy";
    const HOLLIDAY_ON_DEMEND = "Urlop na żądanie";

    public function __construct(
        private VacationRepository $vacationRepository,
        private VacationTypesRepository $typesRepository,
        private BankHolidayRepository $bankHolidayRepository
    ) {
    }

    public function countHolidaysForEmployee(Employee $employee): int
    {

        $holidayType = $this->typesRepository->findBy(['name'=>self::HOLLIDAY])[0];
        $holidayOnRequestType = $this->typesRepository->findBy(['name'=>'Urlop na żądanie'])[0];

        $spendDaysOnRequestType = $this->countVacationSpendDays($employee, $holidayOnRequestType);
        $spendDaysStandardVacation = $this->countVacationSpendDays($employee, $holidayType);

        return $spendDaysStandardVacation + $spendDaysOnRequestType;
    }

    public function countVacationSpendDays(Employee $employee, VacationTypes $vacationType) :int
    {
        $days = 0;


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