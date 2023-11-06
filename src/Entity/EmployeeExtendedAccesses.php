<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Company\Department;
use App\Entity\Company\Employee;
use App\Repository\EmployeeExtendedAccessesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EmployeeExtendedAccessesRepository::class)]
#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['user:read']],security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(normalizationContext: ['groups' => ['user:read']],security: "is_granted('ROLE_ADMIN')"),
        new Post(denormalizationContext: ['groups' => ['user:write']],security: "is_granted('ROLE_ADMIN')"),
        new Put(denormalizationContext: ['groups' => ['user:write']],security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 7
)]
class EmployeeExtendedAccesses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeeExtendedAccesses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(inversedBy: 'employeeExtendedAccesses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employee:read'])]
    private ?Department $department = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

}
