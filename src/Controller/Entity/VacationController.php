<?php

namespace App\Controller\Entity;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Entity\Vacation\VacationTypes;
use App\Repository\Company\EmployeeRepository;
use App\Repository\Vacation\EmployeeVacationLimitRepository;
use App\Repository\Vacation\VacationRepository;
use App\Repository\Vacation\VacationTypesRepository;
use App\Service\Vacation\CounterVacationDays;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VacationController extends AbstractController
{

    public function __construct(
        private readonly CounterVacationDays $counterVacationDays,
        private readonly VacationRepository $vacationRepository,
        private readonly Security $security,
        private readonly EmployeeRepository $employeeRepository,
        private readonly VacationTypesRepository $typesRepository
    )
    {
    }


    #[Route('/api/getCurrentUser/vacations', methods: ['GET'])]
    public function getVacationSum(IriConverterInterface $iriConverter, VacationTypesRepository $typesRepository, EmployeeVacationLimitRepository $employeeVacationLimitRepository, #[CurrentUser] User $user):Response
    {
        $vacationType = $typesRepository->findBy(["name"=>"Urlop Wypoczynkowy"])[0] ?? 0;

        if($vacationType instanceof VacationTypes) {
            $vacationLimit = $employeeVacationLimitRepository->findBy(
                    ["Employee" => $user->getEmployee(), "vacationType" => $vacationType]
                )[0] ?? 0;
            $spendDays = $this->counterVacationDays->countHolidaysForEmployee($user->getEmployee());

            $limit = $vacationLimit instanceof VacationLimits ? $vacationLimit->getDaysLimit() + $vacationLimit->getUnusedDaysFromPreviousYear() : 0;
            $leftVacationDays = $limit - $spendDays;
        }


        return new JsonResponse([
            'spendVacationsDays' => $spendDays ?? 0,
            'vacationDaysLeft' => $leftVacationDays ?? 0,
            'vacationDaysLimit' => $limit ?? 0
        ]);
    }

    #[Route('/api/calendar/vacations')]
    #[IsGranted('ROLE_USER')]
    public function getAllVacationAndSortThem(
        VacationRepository $vacationRepository,
        Request $request,
        Security $security,
        #[CurrentUser] User $user = null
    ): JsonResponse
    {

        if(!$this->security->isGranted('ROLE_ADMIN') ){
            if(!$this->security->isGranted('ROLE_KADR')) {
                $department_id = $user->getEmployee()->getDepartment()->getId();
            }
        }

            $resultDb = $vacationRepository->findAllVacationForCompany(
                $request->query->get('dateFrom') ?? throw new BadRequestException("dateFrom is required"),
                $request->query->get('dateTo') ?? throw new BadRequestException("dateTo is required"),
                $department_id ?? null
            );

        foreach ($resultDb as $vacation) {
            if ($vacation instanceof Vacation) {
                $result[] = [
                    'vacation_iri' => '/api/vacations/' . $vacation->getId(),
                    'employee_iri' => '/api/employees/' . $vacation->getEmployee()->getId(),
                    'employee_name' => $vacation->getEmployee()->getName() ?? "",
                    'employee_surname' => $vacation->getEmployee()->getSurname() ?? "",
                    'dateFrom' => $vacation->getDateFrom()->format('Y-m-d'),
                    'dateTo' => $vacation->getDateTo()->format('Y-m-d'),
                    'type_iri' => '/api/vacation_types/' . $vacation?->getType()?->getId() ?? "",
                    'type_name' => $vacation?->getType()?->getName() ?? "",
                    'status_iri' => '/api/vacation_statuses/' . $vacation?->getStatus()?->getId() ?? "",
                    'status_name' => $vacation?->getStatus()?->getName() ?? "",
                ];
            }
        }

        return new JsonResponse($result ?? []);
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
    public function getSpendDays(int $employeeId): JsonResponse
    {
        $employee = $this->employeeRepository->find($employeeId) ?? throw new BadRequestException("Nie znaleziono pracownika");

        $vacationType = $this->typesRepository->findBy(["name"=>"Urlop Wypoczynkowy"])[0] ?? 0;
        $spendDays = $this->counterVacationDays->countVacationSpendDays($employee,$vacationType);

        $result = [
            'iri' => '/api/vacation_types/'.$vacationType->getId(),
            'type' =>   $vacationType->getName() ?? "Nie znaleziono tego typu urlopu u tego pracownika",
            'spendDays'=> $spendDays ?? ""
        ];

        return new JsonResponse($result ?? []);
    }


}