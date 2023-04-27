<?php

namespace App\Entity;

use App\Repository\RestaurantRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRoleRepository::class)]
class RestaurantRole
{
    public const ADMIN = 'ROLE_ADMIN';
    public const EMPLOYEE = 'ROLE_EMPLOYEE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 30, unique: true)]
    private string $role;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function __toString(): string
    {
        return $this->role;
    }
}
