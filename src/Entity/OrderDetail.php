<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    #[ORM\Id]
    #[ORM\Column(length: 40)]
    private ?string $id = null;

    #[ORM\Column(length: 40)]
    private ?string $itemNumber = null;

    #[ORM\Column(length: 120)]
    private ?string $label = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column]
    private ?float $oraQuantity = null;

    #[ORM\Column(length: 20)]
    private ?string $unity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oraDeliveryDate = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?order $command = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getItemNumber(): ?string
    {
        return $this->itemNumber;
    }

    public function setItemNumber(int $itemNumber): static
    {
        $this->itemNumber = $itemNumber;

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

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOraQuantity(): ?float
    {
        return $this->oraQuantity;
    }

    public function setOraQuantity(float $oraQuantity): static
    {
        $this->oraQuantity = $oraQuantity;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): static
    {
        $this->unity = $unity;

        return $this;
    }

    public function getoraDeliveryDate(): ?\DateTimeInterface
    {
        return $this->oraDeliveryDate;
    }

    public function setoraDeliveryDate(?\DateTimeInterface $oraDeliveryDate): static
    {
        $this->oraDeliveryDate = $oraDeliveryDate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommand(): ?order
    {
        return $this->command;
    }

    public function setCommand(?order $command): static
    {
        $this->command = $command;

        return $this;
    }
}
