<?php

// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Role;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $username;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private Role $role;

    #[ORM\Column(type: "datetime")]
    private \DateTime $createdAt;

    // Геттеры и сеттеры
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
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

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
