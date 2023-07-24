<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** A manufactor
 * @ORM\Entity
 */
class VacationType
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Employee")
     * @ORM\Column(type="integer")
     */
    private int     $id;

    /**
     * Employee Name
     *
     * @ORM\Column(type="string")
     */
    private string  $name = '';


    /**
     * Vacation Type Limit
     *
     * @ORM\Column(type="string")
     */
    private int  $limitInDays = 0;
}