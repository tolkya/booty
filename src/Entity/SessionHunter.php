<?php

namespace App\Entity;

use App\Repository\SessionHunterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionHunterRepository::class)]
class SessionHunter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionHunters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'sessionHunters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hunter $hunter = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    /**
     * @var Collection<int, SessionAnswer>
     */
    #[ORM\OneToMany(targetEntity: SessionAnswer::class, mappedBy: 'sessionHunter', orphanRemoval: true)]
    private Collection $sessionAnswers;

    /**
     * @var Collection<int, SessionLocation>
     */
    #[ORM\OneToMany(targetEntity: SessionLocation::class, mappedBy: 'sessionHunter', orphanRemoval: true)]
    private Collection $sessionLocations;

    public function __construct()
    {
        $this->sessionAnswers = new ArrayCollection();
        $this->sessionLocations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getHunter(): ?Hunter
    {
        return $this->hunter;
    }

    public function setHunter(?Hunter $hunter): static
    {
        $this->hunter = $hunter;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

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

    public function setEndedAt(\DateTimeImmutable $endedAt): static
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
     * @return Collection<int, SessionAnswer>
     */
    public function getSessionAnswers(): Collection
    {
        return $this->sessionAnswers;
    }

    public function addSessionAnswer(SessionAnswer $sessionAnswer): static
    {
        if (!$this->sessionAnswers->contains($sessionAnswer)) {
            $this->sessionAnswers->add($sessionAnswer);
            $sessionAnswer->setSessionHunter($this);
        }

        return $this;
    }

    public function removeSessionAnswer(SessionAnswer $sessionAnswer): static
    {
        if ($this->sessionAnswers->removeElement($sessionAnswer)) {
            // set the owning side to null (unless already changed)
            if ($sessionAnswer->getSessionHunter() === $this) {
                $sessionAnswer->setSessionHunter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SessionLocation>
     */
    public function getSessionLocations(): Collection
    {
        return $this->sessionLocations;
    }

    public function addSessionLocation(SessionLocation $sessionLocation): static
    {
        if (!$this->sessionLocations->contains($sessionLocation)) {
            $this->sessionLocations->add($sessionLocation);
            $sessionLocation->setSessionHunter($this);
        }

        return $this;
    }

    public function removeSessionLocation(SessionLocation $sessionLocation): static
    {
        if ($this->sessionLocations->removeElement($sessionLocation)) {
            // set the owning side to null (unless already changed)
            if ($sessionLocation->getSessionHunter() === $this) {
                $sessionLocation->setSessionHunter(null);
            }
        }

        return $this;
    }
}
