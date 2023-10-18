<?php

namespace App\Controller;

use App\Repository\VacationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VacationController extends AbstractController
{
    public function __construct(
        private VacationRepository $vacationRepository
    )
    {
    }

    #[Route('/api/vacations/week/current')]
    #[IsGranted('ROLE_USER')]
    public function getEmployeeOnVacation() : JsonResponse
    {
        $now = new \DateTime();
        $weekStartDate = clone $now;

        $weekStartDate->modify('this week');
        $weekEndDate = $weekStartDate->modify('+4 days');

        $result = $this->vacationRepository->findEmployeeOnVacation($weekStartDate, $weekEndDate);

        return new JsonResponse($result);
    }
}