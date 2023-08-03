<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;

/** A Employees
 * @ORM\Entity
 */
#[ApiResource(
 operations: [
     new get(),
     new GetCollection()
 ],
    paginationItemsPerPage: 7
)]
class Department
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="department")
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column(type="string")
     */
    private ?string  $name = '';

    /**
     * @return string|null
     */
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}