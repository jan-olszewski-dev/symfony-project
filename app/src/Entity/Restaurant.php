<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Premises::class, orphanRemoval: true)]
    private Collection $premises;

    public function __construct()
    {
        $this->premises = new ArrayCollection();
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

    /**
     * @return Collection<int, Premises>
     */
    public function getPremises(): Collection
    {
        return $this->premises;
    }

    public function addPremise(Premises $premise): self
    {
        if (!$this->premises->contains($premise)) {
            $this->premises->add($premise);
            $premise->setRestaurant($this);
        }

        return $this;
    }

    public function removePremise(Premises $premise): self
    {
        if ($this->premises->removeElement($premise)) {
            // set the owning side to null (unless already changed)
            if ($premise->getRestaurant() === $this) {
                $premise->setRestaurant(null);
            }
        }

        return $this;
    }
}
