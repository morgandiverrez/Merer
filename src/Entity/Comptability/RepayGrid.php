<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\RepayGridRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepayGridRepository::class)]
class RepayGrid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $caracteristique = null;

    #[ORM\Column(length: 255)]
    private ?string $travelMean = null;

    #[ORM\Column(length: 255)]
    private ?string $start = null;

    #[ORM\Column(length: 255)]
    private ?string $end = null;

    #[ORM\Column(precision: 10, scale: 0)]
    private ?float $amount = null;

    #[ORM\Column]
    private ?int $numberPeople = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return ('de '.$this->getStart().' à '.$this->getEnd().' en '.$this->travelMean.' comme '.$this->caracteristique.' à '.$this->numberPeople);
    }

    public function getCaracteristique(): ?string
    {
        return $this->caracteristique;
    }

    public function setCaracteristique(string $caracteristique): self
    {
        $this->caracteristique = $caracteristique;

        return $this;
    }
    

    public function getTravelMean(): ?string
    {
        return $this->travelMean;
    }

    public function setTravelMean(string $travelMean): self
    {
        $this->travelMean = $travelMean;

        return $this;
    }

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function setStart(?string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(?string $end): self
    {
        $this->end = $end;

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

    public function getNumberPeople(): ?int
    {
        return $this->numberPeople;
    }

    public function setNumberPeople(?int $numberPeople): self
    {
        $this->numberPeople = $numberPeople;

        return $this;
    }
}
