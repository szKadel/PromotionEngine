<?php

namespace App\Controller\Api;

use App\Controller\Presist\VacationLimitPresist;
use App\Entity\Vacation\VacationLimits;
use App\Repository\Company\EmployeeRepository;
use App\Repository\Vacation\EmployeeVacationLimitRepository;
use App\Repository\Vacation\VacationTypesRepository;
use App\Service\Vacation\CounterVacationDays;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/',name: 'app_main_mainpage')]
    public function mainPage(): RedirectResponse
    {
        return new RedirectResponse('/view/');
    }

    #[Route('/Vacations/YearSummation/{year}', methods: ['GET'])]
    public function annualYearSummary(EmployeeRepository $employeeRepository, CounterVacationDays $counterVacationDays, EmployeeVacationLimitRepository $employeeVacationLimitRepository,VacationLimitPresist $vacationLimitPresist, VacationTypesRepository $typesRepository, int|string $year): JsonResponse
    {
        $employees = $employeeRepository -> findAll();
        $holidayType = $typesRepository->findBy(['name'=>'Urlop Wypoczynkowy'])[0];

        foreach ($employees as $employee)
        {
            $spendVacationsDays = $counterVacationDays ->countHolidaysForEmployee($employee,$year);
            $limit = $employeeVacationLimitRepository->findBy(['Employee' => $employee,'vacationType'=>$holidayType])[0] ?? 0;

            if($limit instanceof VacationLimits){
                if ($limit->getDaysLimit() > $spendVacationsDays) {
                    $limit->setUnusedDaysFromPreviousYear($limit->getDaysLimit() - $spendVacationsDays);
                    $vacationLimitPresist->add($limit);

                    $result[] = ["name" => $employee->getName(), "UnusedDaysFromPreviousYear" => $limit->getDaysLimit() - $spendVacationsDays];
                }
            }
        }

        return new JsonResponse([$result ?? []]);
    }
}