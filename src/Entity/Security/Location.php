<?php

namespace App\Entity\Security;

use App\Repository\Security\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $warehouse = null;

    #[ORM\Column(length: 12)]
    private ?string $location = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $referent = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 6)]
    private ?string $inventoryNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouse(): ?string
    {
        return $this->warehouse;
    }

    public function setWarehouse(string $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getReferent(): ?string
    {
        return $this->referent;
    }

    public function setReferent(?string $referent): static
    {
        $this->referent = $referent;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInventoryNumber(): ?string
    {
        return $this->inventoryNumber;
    }

    public function setInventoryNumber(string $inventoryNumber): static
    {
        $this->inventoryNumber = $inventoryNumber;

        return $this;
    }
}
