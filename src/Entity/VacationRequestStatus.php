<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** A manufactor
 * @ORM\Entity
 */
class VacationRequestStatus
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OneToMany()
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column(type="string")
     */
    private string  $status_name ;

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