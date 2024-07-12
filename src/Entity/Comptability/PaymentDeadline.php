<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\PaymentDeadlineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentDeadlineRepository::class)]
class PaymentDeadline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $expectedPaymentDate;

    #[ORM\Column(type:'float', precision: 10, scale: 0)]
    private $expectedAmount;

    #[ORM\Column(type: 'string', length: 255)]
    private $expectedMeans;

    #[ORM\Column(type:'date',  nullable: true)]
    private $actualPaymentDate;

    #[ORM\Column(type: 'float', precision: 10, scale: 0 ,  nullable: true)]
    private $actualAmount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $actualMeans;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'paymentDeadlines', cascade: ['persist'])]
    private $invoice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getExpectedPaymentDate();
    }
    
    public function getExpectedPaymentDate(): ?\DateTimeInterface
    {
        return $this->expectedPaymentDate;
    }

    public function setExpectedPaymentDate(\DateTimeInterface $expectedPaymentDate): self
    {
        $this->expectedPaymentDate = $expectedPaymentDate;

        return $this;
    }

    public function getExpectedAmount(): ?float
    {
        return $this->expectedAmount;
    }

    public function setExpectedAmount(float $expectedAmount): self
    {
        $this->expectedAmount = $expectedAmount;

        return $this;
    }

    public function getExpectedMeans(): ?string
    {
        return $this->expectedMeans;
    }

    public function setExpectedMeans(string $expectedMeans): self
    {
        $this->expectedMeans = $expectedMeans;

        return $this;
    }

    public function getActualPaymentDate(): ?\DateTimeInterface
    {
        return $this->actualPaymentDate;
    }

    public function setActualPaymentDate(\DateTimeInterface $actualPaymentDate): self
    {
        $this->actualPaymentDate = $actualPaymentDate;

        return $this;
    }

    public function getActualAmount(): ?float
    {
        return $this->actualAmount;
    }

    public function setActualAmount(?float $actualAmount): self
    {
        $this->actualAmount = $actualAmount;

        return $this;
    }

    public function getActualMeans(): ?string
    {
        return $this->actualMeans;
    }

    public function setActualMeans(?string $actualMeans): self
    {
        $this->actualMeans = $actualMeans;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }
}
