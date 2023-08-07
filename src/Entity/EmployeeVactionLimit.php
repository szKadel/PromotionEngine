<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\EmployeeVactionLimitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeVactionLimitRepository::class)]
#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['vacationLimit:read']]),
        new GetCollection(normalizationContext: ['groups' => ['vacationLimit:read']]),
        new Post(denormalizationContext: ['groups' => ['vacationLimit:write']]),
        new Put(denormalizationContext: ['groups' => ['vacationLimit:write']]),
        new Delete(denormalizationContext: ['groups' => ['vacationLimit:write']])
    ],
    paginationItemsPerPage: 7,
)]
class EmployeeVactionLimit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vacationLimit:read', 'vacationLimit:write','employee:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'type')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationLimit:read', 'vacationLimit:write'])]
    #[Assert\NotBlank]
    private ?Employee $Employee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationLimit:read', 'vacationLimit:write','employee:read'])]
    #[Assert\NotBlank]
    private ?VacationTypes $vacationType = null;

    #[ORM\Column]
    #[Groups(['vacationLimit:read', 'vacationLimit:write','employee:read'])]
    #[Assert\NotBlank]
    private ?int $daysLimit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->Employee;
    }

    public function setEmployee(?Employee $Employee): static
    {
        $this->Employee = $Employee;

        return $this;
    }

    public function getVacationType(): ?VacationTypes
    {
        return $this->vacationType;
    }

    public function setVacationType(?VacationTypes $vacationType): static
    {
        $this->vacationType = $vacationType;

        return $this;
    }

    public function getDaysLimit(): ?int
    {
        return $this->daysLimit;
    }

    public function setDaysLimit(int $daysLimit): static
    {
        $this->daysLimit = $daysLimit;

        return $this;
    }
}
