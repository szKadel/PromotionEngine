<?php

namespace App\Entity\Vacation;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Company\Employee;
use App\Entity\User;
use App\Repository\VacationRepository;
use App\Service\WorkingDaysCounterService;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VacationRepository::class)]
#[ORM\HasLifecycleCallbacks]

#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['vacationRequest:read']],security: "is_granted('ROLE_USER')"),
        new GetCollection(normalizationContext: ['groups' => ['vacationRequest:read']],security: "is_granted('ROLE_USER')"),
        new Post(denormalizationContext: ['groups' => ['vacationRequest:write']],security: "is_granted('ROLE_USER')"),
        new Put(denormalizationContext: ['groups' => ['vacationRequest:update']],security: "is_granted('ROLE_USER')"),
        new Delete(security: "is_granted('ROLE_USER')")
    ],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 7,
)]
#[ApiFilter(OrderFilter::class, properties: ['id','createdAt','acceptedAt','dateFrom','dateTo'])]
#[ApiFilter(SearchFilter::class,properties: ['employee.department'=>'exact','employee.unActive'=>'exact'])]
class Vacation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('vacationRequest:read')]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'vacations')]

    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['vacationRequest:read', 'vacationRequest:write'])]
    #[ApiFilter(SearchFilter::class,strategy: 'exact')]
    private ?Employee $employee = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiFilter(SearchFilter::class,strategy: 'exact')]
    #[Groups(['vacationRequest:read', 'vacationRequest:write','vacationRequest:update'])]
    private ?VacationTypes $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[ApiFilter(DateFilter::class)]
    #[Assert\NotBlank]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Groups(['vacationRequest:read', 'vacationRequest:write','vacationRequest:update'])]
    private ?DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[ApiFilter(DateFilter::class)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write','vacationRequest:update'])]
    private ?DateTimeInterface $dateTo = null;

    #[ORM\Column]
    #[Groups('vacationRequest:read')]
    private ?int $SpendVacationDays = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read', 'vacationRequest:write','vacationRequest:update'])]
    private ?Employee $replacement = null;

    #[ORM\ManyToOne]
    #[ApiFilter(SearchFilter::class,strategy: 'exact')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vacationRequest:read','vacationRequest:update'])]
    private ?VacationStatus $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['vacationRequest:read', 'vacationRequest:write','vacationRequest:update'])]
    private ?string $comment = null;

    #[ORM\Column(type: "datetime",nullable: true)]
    #[Groups(['vacationRequest:read'])]
    private mixed $createdAt = null;

    #[ORM\Column(type: "datetime",nullable: true)]
    #[Groups(['vacationRequest:read'])]
    private mixed $updatedAt = null;

    #[ORM\Column(type: "datetime",nullable: true)]
    #[Groups(['vacationRequest:read'])]
    private mixed $acceptedAt = null;

    #[ORM\Column(type: "datetime",nullable: true)]
    #[Groups(['vacationRequest:read'])]
    private mixed $annulledAt = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read'])]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read'])]
    private ?User $acceptedBy = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read'])]
    private ?User $AnnulledBy = null;

    #[ORM\Column(type: "datetime",nullable: true)]
    #[Groups(['vacationRequest:read'])]
    private mixed $rejectAt = null;

    #[ORM\ManyToOne]
    #[Groups(['vacationRequest:read'])]
    private ?User $rejectBy = null;


    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
    }

    #[ORM\PreUpdate]
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {

        if($this->status?->getName() == "Anulowany")
        {
            $this->setAnnulledAt(new DateTime());
        }

        if($this->status?->getName() == "Potwierdzony")
        {
            $this->setAcceptedAt(new DateTime());
        }

        if($this->status?->getName() == "Odrzucony")
        {
            $this->setRejectAt(new DateTime());
        }
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

    public function getDateFrom(): ?DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(DateTimeInterface $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(DateTimeInterface $dateTo): static
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function getSpendVacationDays(): ?int
    {
        return $this->SpendVacationDays;
    }

    public function setSpendVacationDays(int $SpendVacationDays): static
    {
        $this->SpendVacationDays = $SpendVacationDays;
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

    /**
     * @return mixed
     */
    public function getAnnulledAt()
    {
        return $this->annulledAt;
    }

    /**
     * @param mixed $annulledAt
     */
    public function setAnnulledAt($annulledAt): void
    {
        $this->annulledAt = $annulledAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param mixed $acceptedAt
     */
    public function setAcceptedAt($acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getAcceptedBy(): ?User
    {
        return $this->acceptedBy;
    }

    public function setAcceptedBy(?User $acceptedBy): static
    {
        $this->acceptedBy = $acceptedBy;

        return $this;
    }

    public function getAnnulledBy(): ?User
    {
        return $this->AnnulledBy;
    }

    public function setAnnulledBy(?User $AnnulledBy): static
    {
        $this->AnnulledBy = $AnnulledBy;

        return $this;
    }

    public function getRejectAt(): ?DateTimeInterface
    {
        return $this->rejectAt;
    }

    public function setRejectAt($rejectAt): void
    {
        $this->rejectAt = $rejectAt;
    }

    public function getRejectBy(): ?User
    {
        return $this->rejectBy;
    }

    public function setRejectBy(?User $rejectBy):void
    {
        $this->rejectBy = $rejectBy;
    }
}
