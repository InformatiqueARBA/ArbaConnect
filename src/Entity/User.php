<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $id = null;

    #[ORM\Column(length: 20)]
    private ?string $profil = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $mail = null;

    #[ORM\Column(length: 40)]
    private ?string $firstName = null;

    #[ORM\Column(length: 40)]
    private ?string $lastName = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?corporation $corporation = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $Id): static
    {
        $this->Id = $id;

        return $this;
    }


    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(string $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCorporation(): ?corporation
    {
        return $this->corporation;
    }

    public function setCorporation(?corporation $corporation): static
    {
        $this->corporation = $corporation;

        return $this;
    }
}
