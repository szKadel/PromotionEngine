<?php

namespace App\Controller\Presist;

use App\Entity\Company\Employee;
use App\Entity\Vacation\VacationLimits;
use App\Repository\EmployeeVacationLimitRepository;
use Doctrine\ORM\EntityManagerInterface;

class VacationLimitPresist
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }


    public function remove(VacationLimits $employee)
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