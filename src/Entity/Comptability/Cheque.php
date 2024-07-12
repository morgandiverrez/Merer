<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\Comptability\ChequeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChequeRepository::class)]
class Cheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfCollection = null;

    #[ORM\ManyToOne(inversedBy: 'cheques', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChequeBox $chequeBox = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $quote = null;

    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateOfCollection(): ?\DateTimeInterface
    {
        return $this->dateOfCollection;
    }

    public function setDateOfCollection(\DateTimeInterface $dateOfCollection): self
    {
        $this->dateOfCollection = $dateOfCollection;

        return $this;
    }

    public function getChequeBox(): ?ChequeBox
    {
        return $this->chequeBox;
    }

    public function setChequeBox(?ChequeBox $chequeBox): self
    {
        $this->chequeBox = $chequeBox;

        return $this;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(?string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

   
}
