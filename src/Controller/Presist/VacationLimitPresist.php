<?php

namespace App\Controller\Presist;

use App\Entity\Vacation\VacationLimits;
use Doctrine\ORM\EntityManagerInterface;

class VacationLimitPresist
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }


    public function remove(VacationLimits $employee): void
    {
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }

    public function add(VacationLimits $employee)
    {
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }
}