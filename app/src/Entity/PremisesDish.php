<?php

namespace App\Entity;

use App\Repository\PremisesDishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PremisesDishRepository::class)]
class PremisesDish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'premisesDishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Premises $premises = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dish $dish = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPremises(): ?Premises
    {
        return $this->premises;
    }

    public function setPremises(?Premises $premises): self
    {
        $this->premises = $premises;

        return $this;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

}
