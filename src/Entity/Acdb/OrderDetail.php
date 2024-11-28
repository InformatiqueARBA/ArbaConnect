<?php

namespace App\Entity\Acdb;

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

    #[ORM\Column(length: 6)]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 3)]
    private ?string $lineNumber = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $supplierOrderNumber = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $supplierConfirmation = null;

    #[ORM\Column(length: 6)]
    private ?string $lineType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $receptionDate = null;

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

    public function setItemNumber(string $itemNumber): static
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

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getLineNumber(): ?string
    {
        return $this->lineNumber;
    }

    public function setLineNumber(string $lineNumber): static
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    public function getSupplierOrderNumber(): ?string
    {
        return $this->supplierOrderNumber;
    }

    public function setSupplierOrderNumber(?string $supplierOrderNumber): static
    {
        $this->supplierOrderNumber = $supplierOrderNumber;

        return $this;
    }

    public function getSupplierConfirmation(): ?string
    {
        return $this->supplierConfirmation;
    }

    public function setSupplierConfirmation(?string $supplierConfirmation): static
    {
        $this->supplierConfirmation = $supplierConfirmation;

        return $this;
    }

    public function getLineType(): ?string
    {
        return $this->lineType;
    }

    public function setLineType(string $lineType): static
    {
        $this->lineType = $lineType;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getReceptionDate(): ?\DateTimeInterface
    {
        return $this->receptionDate;
    }

    public function setReceptionDate(?\DateTimeInterface $receptionDate): static
    {
        $this->receptionDate = $receptionDate;

        return $this;
    }
}
