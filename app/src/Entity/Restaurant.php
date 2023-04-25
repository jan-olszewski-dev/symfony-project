<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank()]
    private string $name;

    /** @var Collection<Premises> */
    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Premises::class, orphanRemoval: true)]
    private Collection $premises;

    public function __construct()
    {
        $this->premises = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
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
}
