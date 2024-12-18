<?php

// src/Entity/Grade.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Subject;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[ORM\Table(name: "grades")]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $value;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "student_id", referencedColumnName: "id")]
    private User $student;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(name: "subject_id", referencedColumnName: "id")]
    private Subject $subject;

    // Геттеры и сеттеры
    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getStudent(): User
    {
        return $this->student;
    }

    public function setStudent(User $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): self
    {
        $this->subject = $subject;
        return $this;
    }
}
