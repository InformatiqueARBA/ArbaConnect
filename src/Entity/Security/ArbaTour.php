<?php

namespace App\Entity\Security;

use App\Repository\Security\ArbaTourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArbaTourRepository::class)]
class ArbaTour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 1)]
    private ?string $TourCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getTourCode(): ?string
    {
        return $this->TourCode;
    }

    public function setTourCode(string $TourCode): static
    {
        $this->TourCode = $TourCode;

        return $this;
    }
}
