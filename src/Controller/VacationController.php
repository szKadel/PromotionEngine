<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vacation\VacationLimits;
use App\Repository\EmployeeRepository;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\VacationRepository;
use App\Repository\VacationTypesRepository;
use App\Service\Vacation\CounterVacationDays;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VacationController extends AbstractController
{
    public function __construct(
        private VacationRepository $vacationRepository,
        private Security $security,
        private EmployeeRepository $employeeRepository,
        private VacationTypesRepository $typesRepository,
        private EmployeeVacationLimitRepository $employeeVacationLimitRepository,
         private CounterVacationDays $counterVacationDays
    )
    {
    }

    #[Route('/api/vacations/week/current')]
    #[IsGranted('ROLE_USER')]
    public function getEmployeeOnVacation() : JsonResponse
    {
        $today = date('Y-m-d');

        $monday = date('Y-m-d', strtotime('last Monday', strtotime($today)));
        $friday = date('Y-m-d', strtotime('this Friday', strtotime($today)));

        if($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_KADR')){
            $dbResult = $this->vacationRepository->findEmployeeOnVacation($monday, $friday);
        }else {
            $dbResult = $this->vacationRepository->findEmployeeOnVacation($monday, $friday);
        }

        foreach ($dbResult as $vacation){
            $result[] = [
                'vacation_id'=>$vacation ->getId(),
                'employee_id'=>$vacation ->getEmployee()->getId(),
                'employee_name' => $vacation -> getEmployee()->getName()??"",
                'employee_surname' => $vacation -> getEmployee()->getSurname()??"",
                'department' => $vacation ->getEmployee() -> getDepartment() ?->getName() ??"",
                'dateFrom' => $vacation -> getDateFrom() ->format('Y-m-d'),
                'dateTo' => $vacation -> getDateTo()->format('Y-m-d'),
                'replacement_name' => $vacation ?-> getReplacement() ?-> getName() ??"",
                'replacement_surname' => $vacation ?-> getReplacement() ?-> getSurname()??"",
            ];
        }

        return new JsonResponse($result ?? []);
    }

    #[Route('/api/vacations/{employeeId}/type')]
    #[IsGranted('ROLE_USER')]
    public function getSpendDays(int $employeeId)
    {
        $employee = $this->employeeRepository->find($employeeId) ?? throw new BadRequestException("Nie znaleziono pracownika");

        $vacationType = $this->typesRepository->findBy(["id"=>"Urlop Wypoczynkowy"])[0] ?? 0;
        $vacationLimit = $this->employeeVacationLimitRepository->findBy(["Employee"=>$employee,"vacationType"=>$vacationType])[0]?? 0;
        $spendDays = $vacationLimit instanceof VacationLimits ? $this->counterVacationDays->getVacationDaysSpend($employee,$vacationType) : 0;

        $result = [
            'type' =>   $vacationLimit->getVacationType()->getName(),
            'spendDays'=> $spendDays ?? ""
        ];

        return new JsonResponse($result ?? []);
    }

}