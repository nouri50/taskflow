<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'boards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: BoardMember::class, mappedBy: 'board', cascade: ['remove'])]
    private Collection $boardMembers;

    #[ORM\OneToMany(targetEntity: Column::class, mappedBy: 'board', cascade: ['remove'])]
    private Collection $columns;

    #[ORM\OneToMany(targetEntity: Label::class, mappedBy: 'board', cascade: ['remove'])]
    private Collection $labels;

    public function __construct()
    {
        $this->boardMembers = new ArrayCollection();
        $this->columns = new ArrayCollection();
        $this->labels = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }
    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getBoardMembers(): Collection
    {
        return $this->boardMembers;
    }
    public function addBoardMember(BoardMember $boardMember): static
    {
        if (!$this->boardMembers->contains($boardMember)) {
            $this->boardMembers->add($boardMember);
            $boardMember->setBoard($this);
        }
        return $this;
    }
    public function removeBoardMember(BoardMember $boardMember): static
    {
        if ($this->boardMembers->removeElement($boardMember)) {
            if ($boardMember->getBoard() === $this) {
                $boardMember->setBoard(null);
            }
        }
        return $this;
    }

    public function getColumns(): Collection
    {
        return $this->columns;
    }
    public function addColumn(Column $column): static
    {
        if (!$this->columns->contains($column)) {
            $this->columns->add($column);
            $column->setBoard($this);
        }
        return $this;
    }
    public function removeColumn(Column $column): static
    {
        if ($this->columns->removeElement($column)) {
            if ($column->getBoard() === $this) {
                $column->setBoard(null);
            }
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
            $label->setBoard($this);
        }
        return $this;
    }
    public function removeLabel(Label $label): static
    {
        if ($this->labels->removeElement($label)) {
            if ($label->getBoard() === $this) {
                $label->setBoard(null);
            }
        }
        return $this;
    }
}
