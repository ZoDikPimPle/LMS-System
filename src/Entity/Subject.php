<?php

// src/Entity/Subject.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ORM\Table(name: "subjects")]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "teacher_id", referencedColumnName: "id")]
    private User $teacher;

    // Геттеры и сеттеры
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

    public function getTeacher(): User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): self
    {
        $this->teacher = $teacher;
        return $this;
    }
}
