<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\FederationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FederationRepository::class)]
class Federation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $socialReason = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $statutoryObject = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $representedBy = null;

    #[ORM\Column(length: 15,unique: true, nullable: true)]
    private ?string $rna = null;

    #[ORM\Column(length: 15, unique: true, nullable: true)]
    private ?string $vatNumber = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $currency = null;

    #[ORM\OneToMany(mappedBy: 'federation', targetEntity: Institution::class, cascade:['persist'])]
    private Collection $institutions;


    public function __construct()
    {
        $this->institutions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getSocialReason();
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(?string $socialReason): self
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    public function getStatutoryObject(): ?string
    {
        return $this->statutoryObject;
    }

    public function setStatutoryObject(?string $statutoryObject): self
    {
        $this->statutoryObject = $statutoryObject;

        return $this;
    }



    /**
     * @return Collection<int, Institution>
     */
    public function getInstitutions(): Collection
    {
        return $this->institutions;
    }

    public function addInstitution(Institution $institution): self
    {
        if (!$this->institutions->contains($institution)) {
            $this->institutions->add($institution);
            $institution->setFederation($this);
        }

        return $this;
    }

    public function removeInstitution(Institution $institution): self
    {
        if ($this->institutions->removeElement($institution)) {
            // set the owning side to null (unless already changed)
            if ($institution->getFederation() === $this) {
                $institution->setFederation(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getRepresentedBy(): ?string
    {
        return $this->representedBy;
    }

    public function setRepresentedBy(?string $representedBy): self
    {
        $this->representedBy = $representedBy;

        return $this;
    }

    public function getRna(): ?string
    {
        return $this->rna;
    }

    public function setRna(?string $rna): self
    {
        $this->rna = $rna;

        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

   
}
