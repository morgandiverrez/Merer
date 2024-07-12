<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\FinancementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FinancementRepository::class)]
class Financement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $financeur = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $pluriannuel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVersement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSignature = null;

    #[ORM\Column(nullable: true)]
    private ?bool $flechee = null;

    #[ORM\OneToMany(mappedBy: 'financement', targetEntity: FinancementLine::class, orphanRemoval: true)]
    private Collection $financementLines;

    public function __construct()
    {
        $this->financementLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getName();
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFinanceur(): ?string
    {
        return $this->financeur;
    }

    public function setFinanceur(?string $financeur): self
    {
        $this->financeur = $financeur;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }



    public function getPluriannuel(): ?int
    {
        return $this->pluriannuel;
    }

    public function setPluriannuel(float $pluriannuel): self
    {
        $this->pluriannuel = $pluriannuel;

        return $this;
    }

    public function getDateVersement(): ?\DateTimeInterface
    {
        return $this->dateVersement;
    }

    public function setDateVersement(?\DateTimeInterface $dateVersement): self
    {
        $this->dateVersement = $dateVersement;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->dateSignature;
    }

    public function setDateSignature(?\DateTimeInterface $dateSignature): self
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    public function isFlechee(): ?bool
    {
        return $this->flechee;
    }

    public function setFlechee(?bool $flechee): self
    {
        $this->flechee = $flechee;

        return $this;
    }

    /**
     * @return Collection<int, FinancementLine>
     */
    public function getFinancementLines(): Collection
    {
        return $this->financementLines;
    }

    public function addFinancementLine(FinancementLine $financementLine): self
    {
        if (!$this->financementLines->contains($financementLine)) {
            $this->financementLines->add($financementLine);
            $financementLine->setFinancement($this);
        }

        return $this;
    }

    public function removeFinancementLine(FinancementLine $financementLine): self
    {
        if ($this->financementLines->removeElement($financementLine)) {
            // set the owning side to null (unless already changed)
            if ($financementLine->getFinancement() === $this) {
                $financementLine->setFinancement(null);
            }
        }

        return $this;
    }
}
