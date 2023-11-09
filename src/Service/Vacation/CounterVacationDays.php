<?php

namespace App\Service\Vacation;

use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationStatus;
use App\Entity\Vacation\VacationTypes;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\VacationRepository;
use Composer\XdebugHandler\Status;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CounterVacationDays
{
    public function __construct(
        private VacationRepository $vacationRepository
    )
    {
    }

    public function getVacationDaysSpend(Employee $employee, VacationTypes $vacationType)
    {
        return $this->vacationRepository->findVacationUsedByUser(
            $employee,
            $vacationType
        );
    }
}