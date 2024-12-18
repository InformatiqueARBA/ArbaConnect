<?php

namespace App\Entity\Acdb;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as Acme;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(length: 30)]
    private ?string $id = null;

    #[ORM\Column(length: 20)]
    private ?string $orderStatus = null;

    #[ORM\Column(length: 50)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    // #[Acme\DeliveryDate]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 80)]
    private ?string $seller = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corporation $corporation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $SupplierConfirmation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $SupplierDeliveryDate = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zipCode = null;

    #[ORM\Column(nullable: true)]
    private ?bool $partialDelivery = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR1_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR2_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR3_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CP_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $VIL_chantier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_siege_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR1_siege_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR2_siege_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ADR3_siege_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CP_siege_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $VIL_siege_social = null;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

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

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): static
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function setSeller(string $seller): static
    {
        $this->seller = $seller;

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

    public function getCorporation(): ?Corporation
    {
        return $this->corporation;
    }

    public function setCorporation(?Corporation $corporation): static
    {
        $this->corporation = $corporation;

        return $this;
    }

    public function isSupplierConfirmation(): ?bool
    {
        return $this->SupplierConfirmation;
    }

    public function setSupplierConfirmation(?bool $SupplierConfirmation): static
    {
        $this->SupplierConfirmation = $SupplierConfirmation;

        return $this;
    }

    public function getSupplierDeliveryDate(): ?\DateTimeInterface
    {
        return $this->SupplierDeliveryDate;
    }

    public function setSupplierDeliveryDate(?\DateTimeInterface $SupplierDeliveryDate): static
    {
        $this->SupplierDeliveryDate = $SupplierDeliveryDate;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function isPartialDelivery(): ?bool
    {
        return $this->partialDelivery;
    }

    public function setPartialDelivery(bool $partialDelivery): static
    {
        $this->partialDelivery = $partialDelivery;

        return $this;
    }

    public function getNomChantier(): ?string
    {
        return $this->nom_chantier;
    }

    public function setNomChantier(string $nom_chantier): static
    {
        $this->nom_chantier = $nom_chantier;

        return $this;
    }

    public function getADR1Chantier(): ?string
    {
        return $this->ADR1_chantier;
    }

    public function setADR1Chantier(string $ADR1_chantier): static
    {
        $this->ADR1_chantier = $ADR1_chantier;

        return $this;
    }

    public function getADR2Chantier(): ?string
    {
        return $this->ADR2_chantier;
    }

    public function setADR2Chantier(string $ADR2_chantier): static
    {
        $this->ADR2_chantier = $ADR2_chantier;

        return $this;
    }

    public function getADR3Chantier(): ?string
    {
        return $this->ADR3_chantier;
    }

    public function setADR3Chantier(string $ADR3_chantier): static
    {
        $this->ADR3_chantier = $ADR3_chantier;

        return $this;
    }

    public function getCPChantier(): ?string
    {
        return $this->CP_chantier;
    }

    public function setCPChantier(string $CP_chantier): static
    {
        $this->CP_chantier = $CP_chantier;

        return $this;
    }

    public function getVILChantier(): ?string
    {
        return $this->VIL_chantier;
    }

    public function setVILChantier(string $VIL_chantier): static
    {
        $this->VIL_chantier = $VIL_chantier;

        return $this;
    }

    public function getNomSiegeSocial(): ?string
    {
        return $this->nom_siege_social;
    }

    public function setNomSiegeSocial(string $nom_siege_social): static
    {
        $this->nom_siege_social = $nom_siege_social;

        return $this;
    }

    public function getADR1SiegeSocial(): ?string
    {
        return $this->ADR1_siege_social;
    }

    public function setADR1SiegeSocial(?string $ADR1_siege_social): static
    {
        $this->ADR1_siege_social = $ADR1_siege_social;

        return $this;
    }

    public function getADR2SiegeSocial(): ?string
    {
        return $this->ADR2_siege_social;
    }

    public function setADR2SiegeSocial(?string $ADR2_siege_social): static
    {
        $this->ADR2_siege_social = $ADR2_siege_social;

        return $this;
    }

    public function getADR3SiegeSocial(): ?string
    {
        return $this->ADR3_siege_social;
    }

    public function setADR3SiegeSocial(?string $ADR3_siege_social): static
    {
        $this->ADR3_siege_social = $ADR3_siege_social;

        return $this;
    }

    public function getCPSiegeSocial(): ?string
    {
        return $this->CP_siege_social;
    }

    public function setCPSiegeSocial(?string $CP_siege_social): static
    {
        $this->CP_siege_social = $CP_siege_social;

        return $this;
    }

    public function getVILSiegeSocial(): ?string
    {
        return $this->VIL_siege_social;
    }

    public function setVILSiegeSocial(?string $VIL_siege_social): static
    {
        $this->VIL_siege_social = $VIL_siege_social;

        return $this;
    }
}
