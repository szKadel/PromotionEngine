<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\VacationRepository;
use App\Repository\VacationStatusRepository;
use App\Service\WorkingDaysCounterService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VacationRepository::class)]
#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['vacationRequest:read']]),
        new GetCollection(denormalizationContext: ['groups' => ['vacationRequest:read']]),
        new Post(normalizationContext: ['groups' => ['vacationRequest:write']]),
        new Put(normalizationContext: ['groups' => ['vacationRequest:write']])
    ],
    paginationItemsPerPage: 7,
)]
class Vacation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('vacationRequest:read')]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'vacations')]

    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?Employee $employee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?VacationTypes $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[ApiFilter(DateFilter::class)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[ApiFilter(DateFilter::class)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\Column]
    #[Groups('vacationRequest:read')]
    private ?int $SpendVacationDays = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?Employee $replacement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?VacationStatus $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    private ?string $comment = null;


    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getType(): ?VacationTypes
    {
        return $this->type;
    }

    public function setType(?VacationTypes $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeInterface $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeInterface $dateTo): static
    {
        $this->dateTo = $dateTo;
        $this->setSpendVacationDays();
        return $this;
    }

    public function getSpendVacationDays(): ?int
    {
        return $this->SpendVacationDays;
    }

    private function setSpendVacationDays(): static
    {
        $this->SpendVacationDays = WorkingDaysCounterService::countWorkingDays($this->dateFrom,$this->dateTo);

        return $this;
    }

    public function getReplacement(): ?Employee
    {
        return $this->replacement;
    }

    public function setReplacement(?Employee $replacement): static
    {
        $this->replacement = $replacement;

        return $this;
    }

    public function getStatus(): ?VacationStatus
    {
        return $this->status;
    }

    public function setStatus(?VacationStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;


        return $this;
    }
}
