<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\ExerciceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
   
    #[ORM\Column(unique: true)]
    private ?int $annee;
    
    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: ExpenseReport::class)]
    private Collection $expenseReports;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: BP::class)]
    private Collection $bPs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Impression::class)]
    private Collection $impressions;

   

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->expenseReports = new ArrayCollection();
        $this->bPs = new ArrayCollection();
        $this->impressions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getAnnee();
    }
    
 

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setExercice($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getExercice() === $this) {
                $event->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setExercice($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getExercice() === $this) {
                $invoice->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setExercice($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getExercice() === $this) {
                $transaction->setExercice(null);
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
            $expenseReport->setExercice($this);
        }

        return $this;
    }

    public function removeExpenseReport(ExpenseReport $expenseReport): self
    {
        if ($this->expenseReports->removeElement($expenseReport)) {
            // set the owning side to null (unless already changed)
            if ($expenseReport->getExercice() === $this) {
                $expenseReport->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BP>
     */
    public function getBPs(): Collection
    {
        return $this->bPs;
    }

    public function addBP(BP $bP): self
    {
        if (!$this->bPs->contains($bP)) {
            $this->bPs->add($bP);
            $bP->setExercice($this);
        }

        return $this;
    }

    public function removeBP(BP $bP): self
    {
        if ($this->bPs->removeElement($bP)) {
            // set the owning side to null (unless already changed)
            if ($bP->getExercice() === $this) {
                $bP->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Impression>
     */
    public function getImpressions(): Collection
    {
        return $this->impressions;
    }

    public function addImpression(Impression $impression): self
    {
        if (!$this->impressions->contains($impression)) {
            $this->impressions->add($impression);
            $impression->setExercice($this);
        }

        return $this;
    }

    public function removeImpression(Impression $impression): self
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getExercice() === $this) {
                $impression->setExercice(null);
            }
        }

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }
}
