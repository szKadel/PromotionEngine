<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** A Employees
 * @ORM\Entity
 */
#[ApiResource(
 operations: [
    new get(),
    new GetCollection(),
     new Post(),
     new Put()
])]
class Employee
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\ManyToOne(targetEntity="Employee",inversedBy="id")
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string  $name = '';

    /**
     * Employee Surname
     *
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string  $surname = '';

    /**
     * Employee Department
     *
     * @ORM\ManyToOne(targetEntity="Department",inversedBy="id")
     */
    #[Assert\NotBlank]
    private Department  $department;

    /**
     * User Admin Rights
     *
     * @ORM\Column(type="boolean")
     */
    private ?bool  $isAdmin = false;
    /**
     * Employee Surname
     *
     * @ORM\Column(type="integer")
     */
    private int $vacation_days_limit = 26;

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool|null $isAdmin
     */
    public function setIsAdmin(?bool $isAdmin): void
    {
        $this->isAdmin = true;
    }

    /**
     * @param int| $vacation_days_limit
     */
    public function setVacationDaysLimit(int $vacation_days_limit = 26): void
    {
        $this->vacation_days_limit = $vacation_days_limit;
    }

    /**
     * @return int
     */
    public function getVacationDaysLimit(): int
    {
        return $this->vacation_days_limit;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}