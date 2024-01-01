<?php

namespace App\Controller\Presist;

use App\Entity\Company\Department;
use Doctrine\ORM\EntityManagerInterface;

class DepartmentPresist
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }


    public function add(Department $department): void
    {
        $this->entityManager->persist($department);
        $this->entityManager->flush();
    }
}