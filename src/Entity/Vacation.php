<?php

namespace App\Entity;

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
    private int $id ;

    /**
        * @ORM\ManyToOne(targetEntity="Employee", inversedBy="id")
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="VacationType", inversedBy="id")
     */
    private VacationType $type;

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

    private int $daysLong;

    /**
     * Employee to replace Id
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="id")
     */

    private Employee $replacement;

    /**
     * @ORM\ManyToOne(targetEntity="VacationRequestStatus", inversedBy="id")
     */
    private VacationType $status;

    /**
     * Vacation request comment
     *
     * @ORM\Column(type="text")
     */
    private string   $comment = '';

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
     * @return Employee|null
     */
    public function getReplacement(): Employee
    {
        return $this->replacement;
    }

    public function setReplacement(Employee $replacement): void
    {
        $this->replacement = $replacement;
    }

    /**
     * @return VacationType
     */
    public function getType(): VacationType
    {
        return $this->type;
    }

    /**
     * @param VacationType $type
     */
    public function setType(VacationType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return VacationType
     */
    public function getStatus(): VacationType
    {
        return $this->status;
    }

    /**
     * @param VacationType $status
     */
    public function setStatus(VacationType $status): void
    {
        $this->status = $status;
    }

}