<?php

namespace App\Entity;

use App\Repository\PremisesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PremisesRepository::class)]
class Premises
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'premises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\OneToMany(mappedBy: 'premises', targetEntity: PremisesDish::class)]
    private Collection $premisesDishes;

    public function __construct()
    {
        $this->premisesDishes = new ArrayCollection();
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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * @return Collection<int, PremisesDish>
     */
    public function getPremisesDishes(): Collection
    {
        return $this->premisesDishes;
    }

    public function addPremisesDish(PremisesDish $premisesDish): self
    {
        if (!$this->premisesDishes->contains($premisesDish)) {
            $this->premisesDishes->add($premisesDish);
            $premisesDish->setPremises($this);
        }

        return $this;
    }

    public function removePremisesDish(PremisesDish $premisesDish): self
    {
        if ($this->premisesDishes->removeElement($premisesDish)) {
            // set the owning side to null (unless already changed)
            if ($premisesDish->getPremises() === $this) {
                $premisesDish->setPremises(null);
            }
        }

        return $this;
    }
}
