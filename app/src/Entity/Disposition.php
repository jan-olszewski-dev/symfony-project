<?php

namespace App\Entity;

use App\Repository\DispositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DispositionRepository::class)]
class Disposition
{
    public const STATE_DRAFT = 'draft';
    public const STATE_PAY = 'pay';
    public const STATE_PAID = 'paid';
    public const STATE_PREPARE = 'prepare';
    public const STATE_PREPARED = 'prepared';
    public const STATE_DELIVER = 'deliver';
    public const STATE_DELIVERED = 'delivered';
    public const STATE_CANCELED = 'canceled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 20)]
    private string $status = self::STATE_DRAFT;

    /** @var Collection<Dish> */
    #[ORM\ManyToMany(targetEntity: Dish::class)]
    private Collection $dishes;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Dish>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dish $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes->add($dish);
        }

        return $this;
    }

    public function removeDish(Dish $dish): self
    {
        $this->dishes->removeElement($dish);

        return $this;
    }
}
