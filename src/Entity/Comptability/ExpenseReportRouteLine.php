<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\ExpenseReportRouteLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpenseReportRouteLineRepository::class)]
class ExpenseReportRouteLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $start = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $end = null;

    #[ORM\Column( nullable: true)]
    private ?float $distance = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $travelMean = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    private ?float $amount = null;

    
    #[ORM\ManyToOne( cascade: ['persist'])]
    private ?RepayGrid $RepayGrid = null;

    #[ORM\ManyToOne(inversedBy: 'expenseReportRouteLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExpenseReport $expenseReport = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(string $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getTravelMean(): ?string
    {
        return $this->travelMean;
    }

    public function setTravelMean(string $travelMean): self
    {
        $this->travelMean = $travelMean;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

  

    public function getRepayGrid(): ?RepayGrid
    {
        return $this->RepayGrid;
    }

    public function setRepayGrid(?RepayGrid $RepayGrid): self
    {
        $this->RepayGrid = $RepayGrid;

        return $this;
    }

    public function getExpenseReport(): ?ExpenseReport
    {
        return $this->expenseReport;
    }

    public function setExpenseReport(?ExpenseReport $expenseReport): self
    {
        $this->expenseReport = $expenseReport;

        return $this;
    }
}
