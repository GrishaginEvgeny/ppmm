<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends AbstractUser
{
    #[ORM\Id]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $studyGroup = null;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $institute = null;

    #[ORM\Column]
    #[GreaterThanOrEqual(53)]
    #[LessThanOrEqual(100)]
    private ?int $rating = null;

    /**
     * @var Collection<int, EventStudent>
     */
    #[ORM\OneToMany(targetEntity: EventStudent::class, mappedBy: 'student', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $events;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $login = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
    public function getStudyGroup(): ?string
    {
        return $this->studyGroup;
    }

    public function setStudyGroup(string $studyGroup): static
    {
        $this->studyGroup = $studyGroup;

        return $this;
    }

    public function getInstitute(): ?string
    {
        return $this->institute;
    }

    public function setInstitute(string $institute): static
    {
        $this->institute = $institute;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, EventStudent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(EventStudent $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(EventStudent $event): static
    {
        $this->events->removeElement($event);

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
