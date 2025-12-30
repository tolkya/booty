<?php

namespace App\Entity;

use App\Repository\QuestionChoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionChoiceRepository::class)]
class QuestionChoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questionChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column(length: 500)]
    private ?string $choiceText = null;

    #[ORM\Column]
    private ?bool $isCorrect = null;

    #[ORM\Column]
    private ?int $orderPosition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getChoiceText(): ?string
    {
        return $this->choiceText;
    }

    public function setChoiceText(string $choiceText): static
    {
        $this->choiceText = $choiceText;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getOrderPosition(): ?int
    {
        return $this->orderPosition;
    }

    public function setOrderPosition(int $orderPosition): static
    {
        $this->orderPosition = $orderPosition;

        return $this;
    }
}
