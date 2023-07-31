<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping as ORM;

/** A manufactor
 * @ORM\Entity
 */
#[ApiResource]
class Vacation
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int     $id = null;

    /**
        * @ORM\ManyToOne(targetEntity="Employee", inversedBy="id")
     */
    private Employee $employee;

    /**
     * Vacation start date Y-m-d
     *
     * @ORM\Column(type="datetime")
     */

    private DateTimeInterface    $dateFrom;

    /**
     * Vacation end date Y-m-d
     * @ORM\Column(type="datetime")
     */

    private DateTimeInterface    $dateTo;

    /**
     * Vacation long in days
     *
     * @ORM\Column(type="integer")
     */

    private int      $daysLong;

    /**
     * Employee to replace Id
     *
     * @ORM\Column(type="integer")
     */

    private Employee $replacement;

    /**
     * Vacation request comment
     *
     * @ORM\Column(type="text")
     */
    private string   $comment = '';

    public function __construct(DateTimeInterface $dateFrom,DateTimeInterface $dateTo )
    {
        $this->daysLong = $dateFrom->diff($dateTo)->days;
    }

    /**
     * @return Date|null
     */
    public function getDateFrom(): ?DateTimeInterface
    {
        return $this->dateFrom;
    }

    /**
     * @param Date|null $dateFrom
     */
    public function setDateFrom(?DateTimeInterface $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return Date|null
     */
    public function getDateTo(): ?DateTimeInterface
    {
        return $this->dateTo;
    }

    /**
     * @param Date|null $dateTo
     */
    public function setDateTo(?DateTimeInterface $dateTo): void
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return int
     */
    public function getDaysLong(): int
    {
        return $this->daysLong;
    }

    /**
     * @param int $daysLong
     */
    public function setDaysLong(int $daysLong): void
    {
        $this->daysLong = $daysLong;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Employee
     */
    public function getReplacement(): Employee
    {
        return $this->replacement;
    }

    public function setReplacement(Employee $replacement): void
    {
        $this->replacement = $replacement;
    }

}