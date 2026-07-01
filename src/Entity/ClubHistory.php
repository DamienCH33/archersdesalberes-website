<?php

namespace App\Entity;

use App\Repository\ClubHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubHistoryRepository::class)]
class ClubHistory implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, options: ['default' => 'Notre histoire'])]
    private string $title = 'Notre histoire';

    #[ORM\Column(options: ['default' => 1991])]
    private int $foundingYear = 1991;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getFoundingYear(): int
    {
        return $this->foundingYear;
    }

    public function setFoundingYear(int $foundingYear): static
    {
        $this->foundingYear = $foundingYear;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getYearsOfPassion(): int
    {
        return (int) date('Y') - $this->foundingYear;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
