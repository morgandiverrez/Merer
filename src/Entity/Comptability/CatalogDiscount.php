<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\CatalogDiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogDiscountRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Il y a déjà une reduction avec ce code')]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà une réduction avec ce nom')]
class CatalogDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(length: 15,unique: true, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'string',unique: true, length:50, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'catalogDiscount', targetEntity: InvoiceLine::class, cascade: ['persist'])]
    private $invoiceLines;



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

      public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
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
            $invoiceLine->setCatalogDiscount($this);
        }

        return $this;
    }

    public function removeInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if ($this->invoiceLines->removeElement($invoiceLine)) {
            // set the owning side to null (unless already changed)
            if ($invoiceLine->getCatalogDiscount() === $this) {
                $invoiceLine->setCatalogDiscount(null);
            }
        }

        return $this;
    }

  
}
