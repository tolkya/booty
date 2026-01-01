<?php

namespace App\Entity;

use App\Repository\SessionLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionLocationRepository::class)]
class SessionLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionHunter $sessionHunter = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8)]
    private ?string $longitude = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $recordedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionHunter(): ?SessionHunter
    {
        return $this->sessionHunter;
    }

    public function setSessionHunter(?SessionHunter $sessionHunter): static
    {
        $this->sessionHunter = $sessionHunter;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getRecordedAt(): ?\DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(\DateTimeImmutable $recordedAt): static
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }
}
