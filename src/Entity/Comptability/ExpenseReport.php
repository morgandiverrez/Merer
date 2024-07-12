<?php

namespace App\Entity\Comptability;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Comptability\ExpenseReportLine;
use App\Entity\Comptability\ExpenseReportRouteLine;
use App\Repository\Comptability\ExpenseReportRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ExpenseReportRepository::class)]
class ExpenseReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:1024, nullable: true)]
    private ?string $Motif = null;

    #[ORM\Column(nullable: true, unique: true)]
    private ?int $code = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy:'expenseReports', cascade: ['persist'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(inversedBy:'expenseReports', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\Column]
    private ?bool $comfirm = false;

    #[ORM\ManyToOne(inversedBy: 'expenseReports', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    #[ORM\OneToMany(mappedBy: 'expenseReport', targetEntity: ExpenseReportLine::class, orphanRemoval: true)]
    private Collection $expenseReportLines;

    #[ORM\OneToMany(mappedBy: 'expenseReport', targetEntity: ExpenseReportRouteLine::class, orphanRemoval: true)]
    private Collection $expenseReportRouteLines;

    public function __construct()
    {
        $this->expenseReportRouteLines = new ArrayCollection();
        $this->expenseReportLines = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getMotif(). $this->getId();
    }

    public function getMotif(): ?string
    {
        return $this->Motif;
    }

    public function setMotif(string $Motif): self
    {
        $this->Motif = $Motif;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

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

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * @return Collection<int, ExpenseReportLine>
     */
    public function getExpenseReportLines(): Collection
    {
        return $this->expenseReportLines;
    }

    public function addExpenseReportLine(ExpenseReportLine $expenseReportLines): self
    {
        if (!$this->expenseReportLines->contains($expenseReportLines)) {
            $this->expenseReportLines->add($expenseReportLines);
            $expenseReportLines->setExpenseReport($this);
        }

        return $this;
    }

    public function removeExpenseReportLine(ExpenseReportLine $expenseReportLines): self
    {
        if ($this->expenseReportLines->removeElement($expenseReportLines)) {
            // set the owning side to null (unless already changed)
            if ($expenseReportLines->getExpenseReport() === $this) {
                $expenseReportLines->setExpenseReport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExpenseReportRouteLine>
     */
    public function getExpenseReportRouteLines(): Collection
    {
        return $this->expenseReportRouteLines;
    }

    public function addExpenseReportRouteLine(ExpenseReportRouteLine $expenseReportRouteLine): self
    {
        if (!$this->expenseReportRouteLines->contains($expenseReportRouteLine)) {
            $this->expenseReportRouteLines->add($expenseReportRouteLine);
            $expenseReportRouteLine->setExpenseReport($this);
        }

        return $this;
    }

    public function removeExpenseReportRouteLine(ExpenseReportRouteLine $expenseReportRouteLine): self
    {
        if ($this->expenseReportRouteLines->removeElement($expenseReportRouteLine)) {
            // set the owning side to null (unless already changed)
            if ($expenseReportRouteLine->getExpenseReport() === $this) {
                $expenseReportRouteLine->setExpenseReport(null);
            }
        }

        return $this;
    }

}
