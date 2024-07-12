<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\BankDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankDetailRepository::class)]
class BankDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $IBAN = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $BIC = null;

    #[ORM\ManyToOne(inversedBy: 'bankDetails', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIBAN(): ?string
    {
        return $this->IBAN;
    }

    public function setIBAN(string $IBAN): self
    {
        $this->IBAN = $IBAN;

        return $this;
    }

    public function getBIC(): ?string
    {
        return $this->BIC;
    }

    public function setBIC(?string $BIC): self
    {
        $this->BIC = $BIC;

        return $this;
    }  

    public function getCustomer(): ?customer
    {
        return $this->customer;
    }

    public function setCustomer(?customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
