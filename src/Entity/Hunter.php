<?php

namespace App\Entity;

use App\Repository\HunterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HunterRepository::class)]
class Hunter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, SessionHunter>
     */
    #[ORM\OneToMany(targetEntity: SessionHunter::class, mappedBy: 'hunter')]
    private Collection $sessionHunters;

    public function __construct()
    {
        $this->sessionHunters = new ArrayCollection();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
            $sessionHunter->setHunter($this);
        }

        return $this;
    }

    public function removeSessionHunter(SessionHunter $sessionHunter): static
    {
        if ($this->sessionHunters->removeElement($sessionHunter)) {
            // set the owning side to null (unless already changed)
            if ($sessionHunter->getHunter() === $this) {
                $sessionHunter->setHunter(null);
            }
        }

        return $this;
    }
}
