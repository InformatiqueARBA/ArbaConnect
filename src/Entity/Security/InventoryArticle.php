<?php

namespace App\Entity\Security;

use App\Repository\Security\InventoryArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryArticleRepository::class)]
class InventoryArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 6)]
    private ?string $inventoryNumber = null;

    #[ORM\Column(length: 3)]
    private ?string $warehouse = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $location2 = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $location3 = null;

    #[ORM\Column(length: 15)]
    private ?string $articleCode = null;

    #[ORM\Column(length: 40)]
    private ?string $designation1 = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $designation2 = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $lotCode = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $dimensionType = null;

    #[ORM\Column(nullable: true)]
    private ?float $packaging = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $packagingName = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantityLocation1 = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantityLocation2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantityLocation3 = null;

    #[ORM\Column(length: 5)]
    private ?string $preparationUnit = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity2Location1 = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity2Location2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity2Location3 = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $unitCode = null;

    #[ORM\Column(length: 6)]
    private ?string $typeArticle = null;

    #[ORM\Column]
    private ?bool $divisible = null;

    #[ORM\Column(nullable: true)]
    private ?bool $unknownArticle = null;

    #[ORM\Column(length: 3)]
    private ?string $servedFromStock = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation2(): ?string
    {
        return $this->location2;
    }

    public function setLocation2(?string $location2): static
    {
        $this->location2 = $location2;

        return $this;
    }

    public function getLocation3(): ?string
    {
        return $this->location3;
    }

    public function setLocation3(?string $location3): static
    {
        $this->location3 = $location3;

        return $this;
    }

    public function getArticleCode(): ?string
    {
        return $this->articleCode;
    }

    public function setArticleCode(string $articleCode): static
    {
        $this->articleCode = $articleCode;

        return $this;
    }

    public function getDesignation1(): ?string
    {
        return $this->designation1;
    }

    public function setDesignation1(string $designation1): static
    {
        $this->designation1 = $designation1;

        return $this;
    }

    public function getDesignation2(): ?string
    {
        return $this->designation2;
    }

    public function setDesignation2(?string $designation2): static
    {
        $this->designation2 = $designation2;

        return $this;
    }

    public function getLotCode(): ?string
    {
        return $this->lotCode;
    }

    public function setLotCode(?string $lotCode): static
    {
        $this->lotCode = $lotCode;

        return $this;
    }

    public function getDimensionType(): ?string
    {
        return $this->dimensionType;
    }

    public function setDimensionType(?string $dimensionType): static
    {
        $this->dimensionType = $dimensionType;

        return $this;
    }

    public function getPackaging(): ?float
    {
        return $this->packaging;
    }

    public function setPackaging(?float $packaging): static
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function getPackagingName(): ?string
    {
        return $this->packagingName;
    }

    public function setPackagingName(?string $packagingName): static
    {
        $this->packagingName = $packagingName;

        return $this;
    }

    public function getQuantityLocation1(): ?float
    {
        return $this->quantityLocation1;
    }

    public function setQuantityLocation1(?float $quantityLocation1): static
    {
        $this->quantityLocation1 = $quantityLocation1;

        return $this;
    }

    public function getQuantityLocation2(): ?float
    {
        return $this->quantityLocation2;
    }

    public function setQuantityLocation2(?float $quantityLocation2): static
    {
        $this->quantityLocation2 = $quantityLocation2;

        return $this;
    }

    public function getQuantityLocation3(): ?float
    {
        return $this->quantityLocation3;
    }

    public function setQuantityLocation3(?float $quantityLocation3): static
    {
        $this->quantityLocation3 = $quantityLocation3;

        return $this;
    }

    public function getPreparationUnit(): ?string
    {
        return $this->preparationUnit;
    }

    public function setPreparationUnit(string $preparationUnit): static
    {
        $this->preparationUnit = $preparationUnit;

        return $this;
    }

    public function getQuantity2Location1(): ?float
    {
        return $this->quantity2Location1;
    }

    public function setQuantity2Location1(?float $quantity2Location1): static
    {
        $this->quantity2Location1 = $quantity2Location1;

        return $this;
    }

    public function getQuantity2Location2(): ?float
    {
        return $this->quantity2Location2;
    }

    public function setQuantity2Location2(?float $quantity2Location2): static
    {
        $this->quantity2Location2 = $quantity2Location2;

        return $this;
    }

    public function getQuantity2Location3(): ?float
    {
        return $this->quantity2Location3;
    }

    public function setQuantity2Location3(?float $quantity2Location3): static
    {
        $this->quantity2Location3 = $quantity2Location3;

        return $this;
    }

    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    public function setUnitCode(?string $unitCode): static
    {
        $this->unitCode = $unitCode;

        return $this;
    }

    public function getTypeArticle(): ?string
    {
        return $this->typeArticle;
    }

    public function setTypeArticle(string $typeArticle): static
    {
        $this->typeArticle = $typeArticle;

        return $this;
    }

    public function isDivisible(): ?bool
    {
        return $this->divisible;
    }

    public function setDivisible(bool $divisible): static
    {
        $this->divisible = $divisible;

        return $this;
    }

    public function isUnknownArticle(): ?bool
    {
        return $this->unknownArticle;
    }

    public function setUnknownArticle(?bool $unknownArticle): static
    {
        $this->unknownArticle = $unknownArticle;

        return $this;
    }

    public function getServedFromStock(): ?string
    {
        return $this->servedFromStock;
    }

    public function setServedFromStock(string $servedFromStock): static
    {
        $this->servedFromStock = $servedFromStock;

        return $this;
    }
}
