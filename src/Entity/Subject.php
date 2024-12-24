<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $teacher = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'subjectsStudying')]
    private Collection $students;
    
    public function __construct()
    {
        $this->students = new ArrayCollection();
    }
    
    public function getStudents(): Collection
    {
        return $this->students;
    }
    
    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->addSubjectStudying($this);
        }
    
        return $this;
    }
    
    public function removeStudent(User $student): self
    {
        if ($this->students->removeElement($student)) {
            $student->removeSubjectStudying($this);
        }
    
        return $this;
    }
    

}
