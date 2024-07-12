<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\InstitutionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstitutionRepository::class)]
class Institution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $headquarter = null;

    #[ORM\Column]
    private ?bool $open = null;

    #[ORM\ManyToOne(inversedBy: 'institutions', cascade: ['persist'])]
    private ?AdministrativeIdentifier $administrativeIdentifier = null;

    #[ORM\ManyToOne(inversedBy: 'institutions', cascade: ['persist'])]
    private ?Federation $federation = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Location $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getId();
    }

    public function isHeadquarter(): ?bool
    {
        return $this->headquarter;
    }

    public function setHeadquarter(bool $headquarter): self
    {
        $this->headquarter = $headquarter;

        return $this;
    }

    public function isOpen(): ?bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getAdministrativeIdentifier(): ?AdministrativeIdentifier
    {
        return $this->administrativeIdentifier;
    }

    public function setAdministrativeIdentifier(?AdministrativeIdentifier $administrativeIdentifier): self
    {
        $this->administrativeIdentifier = $administrativeIdentifier;

        return $this;
    }

    public function getFederation(): ?Federation
    {
        return $this->federation;
    }

    public function setFederation(?Federation $federation): self
    {
        $this->federation = $federation;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
