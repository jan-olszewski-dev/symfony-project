<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Validator\Constraints as Assert;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email()]
    #[Assert\NotBlank()]
    private string $email;

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotBlank()]
    private string $firstName;

    #[ORM\Column(length: 70)]
    #[Assert\Length(min: 3, max: 70)]
    #[Assert\NotBlank()]
    private string $lastName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleSubId = null;

    private ?string $plainPassword = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGoogleSubId(): ?string
    {
        return $this->googleSubId;
    }

    public function setGoogleSubId(?string $googleSubId): self
    {
        $this->googleSubId = $googleSubId;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(#[SensitiveParameter] ?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public static function createGoogleUser(GoogleUser $user): self
    {
        return (new self())
            ->setGoogleSubId($user->getId())
            ->setEmail((string)$user->getEmail())
            ->setFirstName((string)$user->getFirstName())
            ->setLastName((string)$user->getLastName());
    }
}
