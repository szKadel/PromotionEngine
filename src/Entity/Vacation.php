<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Tests\Fixtures\ApiResource\User;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

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
        * Employee Id
        *
        * @ORM\Column(type="integer")
     */
    private int $employee;

    /**
     * Vacation start date Y-m-d
     *
     * @ORM\Column(type="datetime")
     */

    private ?DateTimeInterface    $dateFrom;

    /**
     * Vacation end date Y-m-d
     *
     * @ORM\Column(type="datetime")
     */

    private ?DateTimeInterface    $dateTo;

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

    private int     $replacement;

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
    public function setDateFrom(?Date $dateFrom): void
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
    public function setDateTo(?Date $dateTo): void
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
    public function getEmployee(): int
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(int $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return User
     */
    public function getReplacement(): User
    {
        return $this->replacement;
    }

    /**
     * @param User $replacement
     */
    public function setReplacement(User $replacement): void
    {
        $this->replacement = $replacement;
    }
}