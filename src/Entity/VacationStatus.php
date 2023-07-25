<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;

/** A manufactor
 * @ORM\Entity
 */
#[ApiResource(
    operations: [
        new get(),
        new GetCollection()
    ])]
class VacationStatus
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Vacation")
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column(type="string")
     */
    private string  $status_name;

    /**
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        return $this->status_name;
    }

    /**
     * @param string|null $status_name
     */
    public function setStatusName(?string $status_name): void
    {
        $this->status_name = $status_name;
    }
}