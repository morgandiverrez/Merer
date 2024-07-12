<?php

namespace App\Entity\Comptability;

use App\Entity\Comptability\Invoice;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Comptability\TransactionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT ,unique: true)]
    private $code = null;

    #[ORM\Column]
    private ?bool $closure = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $quote = null;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: Invoice::class, cascade: ['persist'])]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: TransactionLine::class, orphanRemoval:true)]
    private Collection $transactionLines;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: ExpenseReport::class,cascade: ['persist'])]
    private Collection $expenseReports;

    #[ORM\ManyToOne(inversedBy: 'transactions', cascade: ['persist'])]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'transactions', cascade: ['persist'])]
    private ?BP $BP = null;

    #[ORM\ManyToOne(inversedBy: 'transactions', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'transactions', cascade: ['persist'])]
    private ?FinancementLine $financementLine = null;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->transactionLines = new ArrayCollection();
        $this->expenseReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getCode();
    }
    

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isClosure(): ?bool
    {
        return $this->closure;
    }

    public function setClosure(bool $closure): self
    {
        $this->closure = $closure;

        return $this;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return Collection<int, invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setTransaction($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getTransaction() === $this) {
                $invoice->setTransaction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transactionline>
     */
    public function getTransactionLines(): Collection
    {
        return $this->transactionLines;
    }

    public function addTransactionLine(TransactionLine $transactionLine): self
    {
        if (!$this->transactionLines->contains($transactionLine)) {
            $this->transactionLines->add($transactionLine);
            $transactionLine->setTransaction($this);
        }

        return $this;
    }

    public function removeTransactionLine(Transactionline $transactionLine): self
    {
        if ($this->transactionLines->removeElement($transactionLine)) {
            // set the owning side to null (unless already changed)
            if ($transactionLine->getTransaction() === $this) {
                $transactionLine->setTransaction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExpenseReport>
     */
    public function getExpenseReports(): Collection
    {
        return $this->expenseReports;
    }

    public function addExpenseReport(ExpenseReport $expenseReport): self
    {
        if (!$this->expenseReports->contains($expenseReport)) {
            $this->expenseReports->add($expenseReport);
            $expenseReport->setTransaction($this);
        }

        return $this;
    }

    public function removeExpenseReport(ExpenseReport $expenseReport): self
    {
        if ($this->expenseReports->removeElement($expenseReport)) {
            // set the owning side to null (unless already changed)
            if ($expenseReport->getTransaction() === $this) {
                $expenseReport->setTransaction(null);
            }
        }

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getBP(): ?BP
    {
        return $this->BP;
    }

    public function setBP(?BP $BP): self
    {
        $this->BP = $BP;

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

    public function getFinancementLine(): ?FinancementLine
    {
        return $this->financementLine;
    }

    public function setFinancementLine(?FinancementLine $financementLine): self
    {
        $this->financementLine = $financementLine;

        return $this;
    }

}
