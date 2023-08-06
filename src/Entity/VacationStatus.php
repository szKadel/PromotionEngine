<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\VacationStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VacationStatusRepository::class)]
#[ApiResource(
    operations: [
        new get(normalizationContext: ['groups' => ['requestStatus:read']]),
        new GetCollection(denormalizationContext: ['groups' => ['requestStatus:read']]),
    ],
    paginationItemsPerPage: 7
)]
class VacationStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['requestStatus:read','requestStatus:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['requestStatus:read', 'requestStatus:write','vacationRequest:read'])]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
