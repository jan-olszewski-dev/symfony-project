<?php

namespace App\Entity;

use App\Repository\RestaurantEmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantEmployeeRepository::class)]
#[ORM\UniqueConstraint(columns: ['employee_id', 'restaurant_id'])]
class RestaurantEmployee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    private Restaurant $restaurant;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'id')]
    #[ORM\JoinColumn(nullable: false)]
    private User $employee;

    /** @var Collection<int, RestaurantRole> */
    #[ORM\JoinTable(name: 'restaurant_employee_role_map')]
    #[ORM\JoinColumn(name: 'restaurant_employee_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: RestaurantRole::class, orphanRemoval: true)]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getEmployee(): User
    {
        return $this->employee;
    }

    public function setEmployee(User $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * @return Collection<int, RestaurantRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(RestaurantRole $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(RestaurantRole $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
