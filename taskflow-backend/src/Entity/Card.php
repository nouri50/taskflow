<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $priority = 'medium';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?Column $boardColumn = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $assignedTo = null;

    #[ORM\OneToMany(targetEntity: ChecklistItem::class, mappedBy: 'card', cascade: ['remove'])]
    private Collection $checklistItems;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'card', cascade: ['remove'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Label::class)]
    private Collection $labels;

    public function __construct()
    {
        $this->checklistItems = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->labels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }
    public function setPriority(string $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }
    public function setDueDate(?\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
    public function setPosition(int $position): static
    {
        $this->position = $position;
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

    public function getBoardColumn(): ?Column
    {
        return $this->boardColumn;
    }
    public function setBoardColumn(?Column $boardColumn): static
    {
        $this->boardColumn = $boardColumn;
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }
    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function getChecklistItems(): Collection
    {
        return $this->checklistItems;
    }
    public function addChecklistItem(ChecklistItem $item): static
    {
        if (!$this->checklistItems->contains($item)) {
            $this->checklistItems->add($item);
            $item->setCard($this);
        }
        return $this;
    }
    public function removeChecklistItem(ChecklistItem $item): static
    {
        if ($this->checklistItems->removeElement($item)) {
            if ($item->getCard() === $this) $item->setCard(null);
        }
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCard($this);
        }
        return $this;
    }
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getCard() === $this) $comment->setCard(null);
        }
        return $this;
    }

    public function getLabels(): Collection
    {
        return $this->labels;
    }
    public function addLabel(Label $label): static
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }
        return $this;
    }
    public function removeLabel(Label $label): static
    {
        $this->labels->removeElement($label);
        return $this;
    }
}
