<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $card_name = null;


    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Deck $deck = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'cards')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Showroom::class, mappedBy: 'cards')]
    private Collection $showrooms;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->showrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardName(): ?string
    {
        return $this->card_name;
    }

    public function setCardName(string $card_name): self
    {
        $this->card_name = $card_name;

        return $this;
    }


    public function getDeck(): ?Deck
    {
        return $this->deck;
    }

    public function setDeck(?Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }

    public function __toString()
    {
        return $this->getCardName();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addCard($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeCard($this);
        }

        return $this;
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
            $showroom->addCard($this);
        }

        return $this;
    }

    public function removeShowroom(Showroom $showroom): self
    {
        if ($this->showrooms->removeElement($showroom)) {
            $showroom->removeCard($this);
        }

        return $this;
    }
}
