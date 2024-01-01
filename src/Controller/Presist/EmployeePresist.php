<?php

namespace App\Controller\Presist;


use App\Entity\Company\Employee;
use Doctrine\ORM\EntityManagerInterface;

class EmployeePresist
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }


    public function add(Employee $employee): void
    {
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }
}