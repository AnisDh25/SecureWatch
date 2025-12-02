<?php

namespace App\Entity;

use App\Repository\AlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $severity = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AlertRule $alertRule = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    private ?Incident $incident = null;

    #[ORM\OneToMany(mappedBy: 'alert', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getSeverity(): ?string
    {
        return $this->severity;
    }

    public function setSeverity(string $severity): static
    {
        $this->severity = $severity;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAlertRule(): ?AlertRule
    {
        return $this->alertRule;
    }

    public function setAlertRule(?AlertRule $alertRule): static
    {
        $this->alertRule = $alertRule;
        return $this;
    }

    public function getIncident(): ?Incident
    {
        return $this->incident;
    }

    public function setIncident(?Incident $incident): static
    {
        $this->incident = $incident;
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setAlert($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getAlert() === $this) {
                $notification->setAlert(null);
            }
        }

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function trigger(): void
    {
        $this->status = 'triggered';
        $this->createdAt = new \DateTime();
    }

    public function assignToIncident(Incident $incident): void
    {
        $this->incident = $incident;
        $this->status = 'escalated';
        $incident->addAlert($this);
    }
}
