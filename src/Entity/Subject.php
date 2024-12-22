<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    // Ваши текущие поля
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    // Связь с преподавателем (OneToMany)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subjectsTeaching')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $teacher = null;

    // Связь со студентами (ManyToMany)
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'subjectsStudying')]
    #[ORM\JoinTable(name: 'subject_students')]
    private Collection $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    // Геттеры и сеттеры для существующих полей

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }

        return $this;
    }

    public function removeStudent(User $student): self
    {
        $this->students->removeElement($student);

        return $this;
    }
}
