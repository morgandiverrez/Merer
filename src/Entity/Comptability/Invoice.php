<?php

namespace App\Entity\Comptability;

use App\Entity\Comptability\InvoiceLine;
use App\Entity\Comptability\PaymentDeadline;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Comptability\Comptability\InvoiceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $creationDate;

    #[ORM\Column(type: 'boolean')]
    private $acquitted = false; // payé

    #[ORM\Column(type: 'boolean')]
    private $ready = false; // visible

    #[ORM\Column(type: 'boolean')]
    private $comfirm = false; // validé

    #[ORM\Column(type: 'boolean')]
    private $credit = false; // pour les avoirs

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null; 

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $category = null; // pour impression par exemple

    #[ORM\ManyToOne(inversedBy: 'invoices', cascade: ['persist'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(inversedBy: 'invoices', cascade: ['persist'])]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: PaymentDeadline::class, orphanRemoval: true)]
    private $paymentDeadlines;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceLine::class, orphanRemoval:true)]
    private $invoiceLines;

    #[ORM\ManyToOne(inversedBy: 'invoices', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Impression::class, cascade: ['persist'])]
    private Collection $impressions;


    public function __construct()
    {
        $this->paymentDeadlines = new ArrayCollection();
        $this->invoiceLines = new ArrayCollection();
        $this->impressions = new ArrayCollection();
    }


    public function  __toString()
    {
        return $this->getCode();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function isAcquitted(): ?bool
    {
        return $this->acquitted;
    }

    public function setAcquitted(?bool $acquitted): self
    {
        $this->acquitted = $acquitted;

        return $this;
    }

    public function isReady(): ?bool
    {
        return $this->ready;
    }

    public function setReady(?bool $ready): self
    {
        $this->ready = $ready;

        return $this;
    }

    public function isComfirm(): ?bool
    {
        return $this->comfirm;
    }

    public function setComfirm(?bool $comfirm): self
    {
        $this->comfirm = $comfirm;

        return $this;
    }

    /**
     * @return Collection<int, Impressions>
     */
    public function getImpressions(): Collection
    {
        return $this->impressions;
    }

    public function addImpression(Impression $impression): self
    {
        if (!$this->impressions->contains($impression)) {
            $this->impressions->add($impression);
            $impression->setInvoice($this);
        }

        return $this;
    }

    public function removeImpression(Impression $impression): self
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getInvoice() === $this) {
                $impression->setInvoice(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, paymentDeadline>
     */
    public function getPaymentDeadlines(): Collection
    {
        return $this->paymentDeadlines;
    }

    public function addPaymentDeadline(PaymentDeadline $paymentDeadline): self
    {
        if (!$this->paymentDeadlines->contains($paymentDeadline)) {
            $this->paymentDeadlines[] = $paymentDeadline;
            $paymentDeadline->setInvoice($this);
        }

        return $this;
    }

    public function removePaymentDeadline(paymentDeadline $paymentDeadline): self
    {
        if ($this->paymentDeadlines->removeElement($paymentDeadline)) {
            // set the owning side to null (unless already changed)
            if ($paymentDeadline->getInvoice() === $this) {
                $paymentDeadline->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvoiceLine>
     */
    public function getInvoiceLines(): Collection
    {
        return $this->invoiceLines;
    }

    public function addInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if (!$this->invoiceLines->contains($invoiceLine)) {
            $this->invoiceLines[] = $invoiceLine;
            $invoiceLine->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if ($this->invoiceLines->removeElement($invoiceLine)) {
            // set the owning side to null (unless already changed)
            if ($invoiceLine->getInvoice() === $this) {
                $invoiceLine->setInvoice(null);
            }
        }

        return $this;
    }

    public function isCredit(): ?bool
    {
        return $this->credit;
    }

    public function setCredit(?bool $credit): self
    {
        $this->credit = $credit;

        return $this;
    }


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;

        return $this;
    }
}
