<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    // Связь с дисциплинами, которые ведет
    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Subject::class)]
    private Collection $subjectsTeaching;

    // Связь с дисциплинами, которые посещает
    #[ORM\ManyToMany(mappedBy: 'students', targetEntity: Subject::class)]
    private Collection $subjectsStudying;

    public function __construct()
    {
        $this->subjectsTeaching = new ArrayCollection();
        $this->subjectsStudying = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Если вы храните временные или чувствительные данные на объекте пользователя, очистите их здесь.
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjectsTeaching(): Collection
    {
        return $this->subjectsTeaching;
    }

    public function addSubjectTeaching(Subject $subject): self
    {
        if (!$this->subjectsTeaching->contains($subject)) {
            $this->subjectsTeaching->add($subject);
            $subject->setTeacher($this);
        }

        return $this;
    }

    public function removeSubjectTeaching(Subject $subject): self
    {
        if ($this->subjectsTeaching->removeElement($subject)) {
            if ($subject->getTeacher() === $this) {
                $subject->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjectsStudying(): Collection
    {
        return $this->subjectsStudying;
    }

    public function addSubjectStudying(Subject $subject): self
    {
        if (!$this->subjectsStudying->contains($subject)) {
            $this->subjectsStudying->add($subject);
            $subject->addStudent($this);
        }

        return $this;
    }

    public function removeSubjectStudying(Subject $subject): self
    {
        if ($this->subjectsStudying->removeElement($subject)) {
            $subject->removeStudent($this);
        }

        return $this;
    }
}
