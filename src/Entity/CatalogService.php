<?php

namespace App\Entity;

use App\Repository\CatalogServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogServiceRepository::class)]
class CatalogService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type:'float', precision: 10, scale: 0)]
    private $amountHt;

    #[ORM\Column(type:'float', precision: 10, scale: 0)]
    private $tvaRate;

    #[ORM\Column(type:'float', precision: 10, scale: 0)]
    private $amountTtc;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'CatalogService', targetEntity: InvoiceLine::class)]
    private $invoiceLines;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    public function __construct()
    {
        $this->invoiceLines = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->getName();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmountHt(): ?float
    {
        return $this->amountHt;
    }

    public function setAmountHt(float $amountHt): self
    {
        $this->amountHt = $amountHt;

        return $this;
    }

    public function getTvaRate(): ?float
    {
        return $this->tvaRate;
    }

    public function setTvaRate(float $tvaRate): self
    {
        $this->tvaRate = $tvaRate;

        return $this;
    }

    public function getAmountTtc(): ?float
    {
        return $this->amountTtc;
    }

    public function setAmountTtc(float $amountTtc): self
    {
        $this->amountTtc = $amountTtc;

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
            $invoiceLine->setCatalogService($this);
        }

        return $this;
    }

    public function removeInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if ($this->invoiceLines->removeElement($invoiceLine)) {
            // set the owning side to null (unless already changed)
            if ($invoiceLine->getCatalogService() === $this) {
                $invoiceLine->setCatalogService(null);
            }
        }

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
}