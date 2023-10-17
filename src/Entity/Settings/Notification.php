<?php

namespace App\Entity\Settings;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Settings\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotificateAdminOnAcceptVacation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotificateAdminOnVacationRequest = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotificateDepartmentModOnCreatedVacation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotificateReplacementUser = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotificateUserOnVacationRequestAccept = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNotificateAdminOnAcceptVacation(): ?bool
    {
        return $this->NotificateAdminOnAcceptVacation;
    }

    public function setNotificateAdminOnAcceptVacation(?bool $NotificateAdminOnAcceptVacation): static
    {
        $this->NotificateAdminOnAcceptVacation = $NotificateAdminOnAcceptVacation;

        return $this;
    }

    public function isNotificateDepartmentModOnCreatedVacation(): ?bool
    {
        return $this->NotificateDepartmentModOnCreatedVacation;
    }

    public function setNotificateDepartmentModOnCreatedVacation(?bool $NotificateDepartmentModOnCreatedVacation): static
    {
        $this->NotificateDepartmentModOnCreatedVacation = $NotificateDepartmentModOnCreatedVacation;

        return $this;
    }

    public function isNotificateReplacementUser(): ?bool
    {
        return $this->NotificateReplacementUser;
    }

    public function setNotificateReplacementUser(?bool $NotificateReplacementUser): static
    {
        $this->NotificateReplacementUser = $NotificateReplacementUser;

        return $this;
    }

    public function isNotificateUserOnVacationRequestAccept(): ?bool
    {
        return $this->NotificateUserOnVacationRequestAccept;
    }

    public function setNotificateUserOnVacationRequestAccept(?bool $NotificateUserOnVacationRequestAccept): static
    {
        $this->NotificateUserOnVacationRequestAccept = $NotificateUserOnVacationRequestAccept;

        return $this;
    }

    public function getNotificateAdminOnVacationRequest(): ?bool
    {
        return $this->NotificateAdminOnVacationRequest;
    }

    public function setNotificateAdminOnVacationRequest(?bool $NotificateAdminOnVacationRequest): void
    {
        $this->NotificateAdminOnVacationRequest = $NotificateAdminOnVacationRequest;
    }
}
