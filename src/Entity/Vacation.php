<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Service\WorkingDaysCounterService;
use DateTimeInterface;
use Symfony\Component\Security\Core\Signature\Exception\ExpiredSignatureException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** A manufactor
 * @ORM\Entity
 */
#[ApiResource(
    operations: [
        new get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    paginationItemsPerPage: 7
)]
class Vacation
{
    /**
     * Vacation Request Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Assert\Blank]
    private int $id ;

    /**
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="id")
     */
    #[Assert\NotBlank]
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="VacationType", inversedBy="id")
     */
    #[Assert\NotBlank]
    private VacationType $type;

    /**
     * Vacation start date Y-m-d
     *
     * @ORM\Column(type="datetime")
     */

    #[ApiFilter(DateFilter::class)]
    #[Groups('vacation:read')]
    private DateTimeInterface    $dateFrom;

    /**
     * Vacation end date Y-m-d
     * @ORM\Column(type="datetime")
     */

    #[ApiFilter(DateFilter::class)]
    #[Groups('vacation:read')]
    private DateTimeInterface    $dateTo;

    /**
     * Vacation long in days
     *
     * @ORM\Column(type="integer")
     */

    #[Groups('vacation:read')]
    private ?int $daysLong =0 ;

    /**
     * Employee to replace Id
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="id")
     */

    #[Assert\NotBlank]


    private Employee $replacement;

    /**
     * @ORM\ManyToOne(targetEntity="VacationStatus", inversedBy="id")
     */
    private VacationStatus $status;

    /**
     * Vacation request comment
     *
     * @ORM\Column(type="text")
     */
    private string   $comment = '';

    public function __construct()
    {

    }

    /**
     * @return Date|null
     */
    public function getDateFrom(): DateTimeInterface
    {
        return $this->dateFrom;
    }

    /**
     * @param Date|null $dateFrom
     */
    public function setDateFrom(DateTimeInterface $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return Date|null
     */
    public function getDateTo(): DateTimeInterface
    {
        return $this->dateTo;
    }

    /**
     * @param Date|null $dateTo
     */
    public function setDateTo(DateTimeInterface $dateTo): void
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return ?int
     */
    public function getDaysLong(): ?int
    {
        return $this->daysLong;
    }

    /**
     * @param ?int $daysLong
     */
    public function setDaysLong(?int $daysLong): void
    {
        $this->daysLong = WorkingDaysCounterService::countWorkingDays($this->dateFrom,$this->dateTo);
            $this->dateFrom->diff($this->dateTo)->days;
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
     * @return VacationStatus
     */
    public function getStatus(): VacationStatus
    {
        return $this->status;
    }

    /**
     * @param VacationStatus $status
     */
    public function setStatus(VacationStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}