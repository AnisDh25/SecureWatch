<?php

namespace App\Entity;

use App\Repository\AlertRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlertRuleRepository::class)]
class AlertRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $condition = null;

    #[ORM\Column]
    private ?int $threshold = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\OneToMany(mappedBy: 'alertRule', targetEntity: Alert::class)]
    private Collection $alerts;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): static
    {
        $this->condition = $condition;
        return $this;
    }

    public function getThreshold(): ?int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): static
    {
        $this->threshold = $threshold;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return Collection<int, Alert>
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): static
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->setAlertRule($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): static
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getAlertRule() === $this) {
                $alert->setAlertRule(null);
            }
        }

        return $this;
    }

    public function evaluate(Event $event): bool
    {
        // Simple evaluation logic - can be extended
        switch ($this->condition) {
            case 'severity_high':
                return $event->getSeverity() === 'high';
            case 'severity_critical':
                return $event->getSeverity() === 'critical';
            case 'source_match':
                return $event->getSource() === $this->action;
            default:
                return false;
        }
    }
}
