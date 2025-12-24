<?php

namespace App\Entity;

use App\Enum\Status;
use App\Repository\EventStudentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: EventStudentRepository::class)]
#[ORM\Table(name: 'event_student')]
class EventStudent
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Event::class, fetch: 'EAGER', inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Student::class, fetch: 'EAGER', inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private Student $student;

    #[ORM\Column(enumType: Status::class)]
    #[NotBlank]
    private ?Status $status = Status::ON_CHECK;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param Student $student
     */
    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }
}
