<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Deck::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $decks;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Showroom::class, orphanRemoval: true)]
    private Collection $showrooms;

    #[ORM\OneToOne(inversedBy: 'member', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
        $this->showrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Deck>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): self
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->setMember($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): self
    {
        if ($this->decks->removeElement($deck)) {
            // set the owning side to null (unless already changed)
            if ($deck->getMember() === $this) {
                $deck->setMember(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection<int, Showroom>
     */
    public function getShowrooms(): Collection
    {
        return $this->showrooms;
    }

    public function addShowroom(Showroom $showroom): self
    {
        if (!$this->showrooms->contains($showroom)) {
            $this->showrooms->add($showroom);
            $showroom->setOwner($this);
        }

        return $this;
    }

    public function removeShowroom(Showroom $showroom): self
    {
        if ($this->showrooms->removeElement($showroom)) {
            // set the owning side to null (unless already changed)
            if ($showroom->getOwner() === $this) {
                $showroom->setOwner(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
