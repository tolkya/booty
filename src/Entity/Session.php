<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hunt $hunt = null;

    #[ORM\Column]
    private ?int $maxDuration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    /**
     * @var Collection<int, SessionHunter>
     */
    #[ORM\OneToMany(targetEntity: SessionHunter::class, mappedBy: 'session')]
    private Collection $sessionHunters;

    public function __construct()
    {
        $this->sessionHunters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHunt(): ?Hunt
    {
        return $this->hunt;
    }

    public function setHunt(?Hunt $hunt): static
    {
        $this->hunt = $hunt;

        return $this;
    }

    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(int $maxDuration): static
    {
        $this->maxDuration = $maxDuration;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, SessionHunter>
     */
    public function getSessionHunters(): Collection
    {
        return $this->sessionHunters;
    }

    public function addSessionHunter(SessionHunter $sessionHunter): static
    {
        if (!$this->sessionHunters->contains($sessionHunter)) {
            $this->sessionHunters->add($sessionHunter);
            $sessionHunter->setSession($this);
        }

        return $this;
    }

    public function removeSessionHunter(SessionHunter $sessionHunter): static
    {
        if ($this->sessionHunters->removeElement($sessionHunter)) {
            // set the owning side to null (unless already changed)
            if ($sessionHunter->getSession() === $this) {
                $sessionHunter->setSession(null);
            }
        }

        return $this;
    }
}
