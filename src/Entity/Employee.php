<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['employee:read']]),
        new GetCollection(normalizationContext: ['groups' => ['employee:read']]),
        new Post(normalizationContext: ['groups' => ['employee:write']]),
        new Put(normalizationContext: ['groups' => ['employee:write']])
    ],
    paginationItemsPerPage: 7
)]

class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employee:read','vacationLimit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['employee:read','employee:write','vacationRequest:read','vacationLimit:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(['employee:read','employee:write','vacationRequest:read','vacationLimit:read'])]
    private ?string $surname = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee:read'])]
    private ?bool $isAdmin = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[Assert\NotBlank]
    #[Groups(['employee:read','employee:write'])]
    private ?Department $department = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read','employee:write'])]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'Employee', targetEntity: EmployeeVactionLimit::class, orphanRemoval: true)]
    #[Groups(['employee:read'])]
    private Collection $vacationLimits;

    #[ORM\OneToMany(mappedBy: 'Employee', targetEntity: Vacation::class)]
    private Collection $vacations;

    public function __construct()
    {
        $this->vacationLimits = new ArrayCollection();
        $this->vacations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, EmployeeVactionLimit>
     */
    public function getVacationLimits(): Collection
    {
        return $this->vacationLimits;
    }

    public function addType(EmployeeVactionLimit $vacationLimit): static
    {
        if (!$this->vacationLimits->contains($vacationLimit)) {
            $this->vacationLimits->add($vacationLimit);
            $vacationLimit->setEmployee($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vacation>
     */
    public function getVacations(): Collection
    {
        return $this->vacations;
    }

    public function addVacation(Vacation $vacation): static
    {
        if (!$this->vacations->contains($vacation)) {
            $this->vacations->add($vacation);
            $vacation->setEmployee($this);
        }

        return $this;
    }

    public function removeVacation(Vacation $vacation): static
    {
        if ($this->vacations->removeElement($vacation)) {
            // set the owning side to null (unless already changed)
            if ($vacation->getEmployee() === $this) {
                $vacation->setEmployee(null);
            }
        }

        return $this;
    }
}
