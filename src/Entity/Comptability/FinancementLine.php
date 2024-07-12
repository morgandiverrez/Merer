<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\FinancementLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FinancementLineRepository::class)]
class FinancementLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $libellee = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    private ?float $Amount = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy:'financementLines', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Financement $financement = null;

    #[ORM\Column(nullable: true)]
    private ?float $reporter = null;

    #[ORM\OneToMany(mappedBy: 'financementLine', targetEntity: Transaction::class, cascade: ['persist'])]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'financementLine', targetEntity: Event::class, cascade: ['persist'])]
    private Collection $events;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function  __toString()
    {
        return ($this->getFinancement()->getName() . '_' . $this->getLibellee());
    }

    public function getLibellee(): ?string
    {
        return $this->libellee;
    }

    public function setLibellee(string $libellee): self
    {
        $this->libellee = $libellee;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->Amount;
    }

    public function setAmount(float $Amount): self
    {
        $this->Amount = $Amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFinancement(): ?Financement
    {
        return $this->financement;
    }

    public function setFinancement(?Financement $financement): self
    {
        $this->financement = $financement;

        return $this;
    }

    public function getReporter(): ?float
    {
        return $this->reporter;
    }

    public function setReporter(?float $reporter): self
    {
        $this->reporter = $reporter;

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
            $transaction->setFinancementLine($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getFinancementLine() === $this) {
                $transaction->setFinancementLine(null);
            }
        }

        return $this;
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
            $event->setFinancementLine($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getFinancementLine() === $this) {
                $event->setFinancementLine(null);
            }
        }

        return $this;
    }
}
