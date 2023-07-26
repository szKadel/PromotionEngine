<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;

/** A manufactor
 * @ORM\Entity
 */
#[ApiResource(
    operations: [
        new get(),
        new GetCollection()
    ])]
class VacationType
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Vacation", mappedBy="type")
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column
     */
    private string  $name = '';


    /**
     * Vacation Type Limit
     *
     * @ORM\Column
     */
    private int  $limitInDays = 0;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLimitInDays(): int
    {
        return $this->limitInDays;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}