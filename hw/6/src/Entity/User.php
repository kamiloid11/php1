<?php

namespace App\Entity;

use App\Validator\StrongPassword;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'такая почта уже зарегистрирована')]
#[StrongPassword]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'почта не заполнена')]
    #[Assert\Email(message: 'почта указана неверно')]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'имя не заполнено')]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'имя короче {{ limit }} символов',
        maxMessage: 'имя длиннее {{ limit }} символов',
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}-]+$/u',
        message: 'в имени допустимы только буквы и дефис',
    )]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $password = null;

    //пароль в открытом виде, в базу не попадает: живёт только между формой и хешированием
    #[Assert\NotBlank(message: 'пароль не заполнен')]
    #[Assert\Length(min: 8, minMessage: 'пароль короче {{ limit }} символов')]
    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
