<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Schedule implements \Stringable
{
    public const CATEGORIES = [
        'Jeunes débutants' => 'Jeunes débutants',
        'Jeunes confirmés' => 'Jeunes confirmés',
        'Adultes' => 'Adultes',
        'Section Banyuls-sur-Mer' => 'Section Banyuls-sur-Mer',
    ];

    public const DAYS = [
        'Lundi' => 'Lundi',
        'Mardi' => 'Mardi',
        'Mercredi' => 'Mercredi',
        'Jeudi' => 'Jeudi',
        'Vendredi' => 'Vendredi',
        'Samedi' => 'Samedi',
        'Dimanche' => 'Dimanche',
    ];

    public const SEASONS = [
        'Toute l\'année' => 'Toute l\'année',
        'Hiver' => 'Hiver',
        'Été' => 'Été',
    ];

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\Column(length: 20)]
    private ?string $day = null;

    #[ORM\Column(length: 20)]
    private string $season = 'Toute l\'année';

    #[ORM\Column(type: 'time_immutable')]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: 'time_immutable')]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $winterLocation = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $summerLocation = null;

    #[ORM\Column]
    private int $displayOrder = 0;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): static
    {
        $this->season = $season;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getWinterLocation(): ?string
    {
        return $this->winterLocation;
    }

    public function setWinterLocation(?string $winterLocation): static
    {
        $this->winterLocation = $winterLocation;

        return $this;
    }

    public function getSummerLocation(): ?string
    {
        return $this->summerLocation;
    }

    public function setSummerLocation(?string $summerLocation): static
    {
        $this->summerLocation = $summerLocation;

        return $this;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

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
        return ($this->category ?? '').' — '.($this->day ?? '');
    }
}
