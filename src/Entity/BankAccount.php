<?php

namespace App\Entity;

use App\Repository\BankAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankAccountRepository::class)]
class BankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $accountNumber = null;

    #[ORM\Column]
    private ?int $ribBankCode = null;

    #[ORM\Column]
    private ?int $ribBranchCode = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $ribAccountNumber = null;

    #[ORM\Column]
    private ?int $ribKey = null;

    #[ORM\Column(length: 64)]
    private ?string $iban = null;

    #[ORM\Column(length: 64)]
    private ?string $bic = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $lastCountDate = null;

    #[ORM\ManyToOne]
    private ?ChartOfAccounts $chartOfAccounts = null;

    #[ORM\ManyToOne]
    private ?Location $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?int
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(int $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getRibBankCode(): ?int
    {
        return $this->ribBankCode;
    }

    public function setRibBankCode(int $ribBankCode): self
    {
        $this->ribBankCode = $ribBankCode;

        return $this;
    }

    public function getRibBranchCode(): ?int
    {
        return $this->ribBranchCode;
    }

    public function setRibBranchCode(int $ribBranchCode): self
    {
        $this->ribBranchCode = $ribBranchCode;

        return $this;
    }

    public function getRibAccountNumber(): ?string
    {
        return $this->ribAccountNumber;
    }

    public function setRibAccountNumber(string $ribAccountNumber): self
    {
        $this->ribAccountNumber = $ribAccountNumber;

        return $this;
    }

    public function getRibKey(): ?int
    {
        return $this->ribKey;
    }

    public function setRibKey(int $ribKey): self
    {
        $this->ribKey = $ribKey;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    public function getLastCountDate(): ?\DateTimeInterface
    {
        return $this->lastCountDate;
    }

    public function setLastCountDate(\DateTimeInterface $lastCountDate): self
    {
        $this->lastCountDate = $lastCountDate;

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