<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\BPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BPRepository::class)]
class BP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column(length:1024, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column(  precision: 10, scale:0, nullable: true)]
    private ?float $expectedAmount = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    private ?float $reallocateAmount = null;

    #[ORM\OneToMany(mappedBy: 'BP', targetEntity: Transaction::class, cascade: ['persist'])]
    private Collection $transactions;

    #[ORM\ManyToOne(inversedBy: 'bPs', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

      public function  __toString()
    {
        return $this->getDesignation();
    }
    
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

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

    public function getReallocateAmount(): ?float
    {
        return $this->reallocateAmount;
    }

    public function setReallocateAmount(?float $reallocateAmount): self
    {
        $this->reallocateAmount = $reallocateAmount;

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
            $transaction->setBP($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getBP() === $this) {
                $transaction->setBP(null);
            }
        }

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
