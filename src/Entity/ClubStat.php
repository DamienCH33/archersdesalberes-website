<?php

namespace App\Entity;

use App\Repository\ClubStatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ClubStatRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ClubStat
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $statKey = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $statValue = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStatKey(): ?string
    {
        return $this->statKey;
    }

    public function setStatKey(string $statKey): static
    {
        $this->statKey = $statKey;
        return $this;
    }

    public function getStatValue(): ?int
    {
        return $this->statValue;
    }

    public function setStatValue(int $statValue): static
    {
        $this->statValue = $statValue;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->label ?? 'Statistique';
    }
}
