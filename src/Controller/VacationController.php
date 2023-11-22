<?php

namespace App\Controller;

use App\Repository\VacationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VacationController extends AbstractController
{
    public function __construct(
        private VacationRepository $vacationRepository,
        private Security $security
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


}