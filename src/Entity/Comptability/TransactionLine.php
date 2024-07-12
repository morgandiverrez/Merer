<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\TransactionLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionLineRepository::class)]
class TransactionLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(precision: 10, scale: 0)]
    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlProof = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $quote = null;

    #[ORM\ManyToOne(inversedBy: 'transactionLines', cascade: ['persist'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(inversedBy: 'transactionLines', cascade: ['persist'])]
    private ?ChartOfAccounts $chartOfAccounts = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getLabel();
    }
    

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getUrlProof(): ?string
    {
        return $this->urlProof;
    }

    public function setUrlProof(string $urlProof): self
    {
        $this->urlProof = $urlProof;

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

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getChartOfAccounts(): ?ChartOfAccounts
    {
        return $this->chartOfAccounts;
    }

    public function setChartOfAccounts(?ChartOfAccounts $chartOfAccounts): self
    {
        $this->chartOfAccounts = $chartOfAccounts;

        return $this;
    }
}
