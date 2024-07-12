<?php

namespace App\Entity\Comptability;

use App\Entity\User;
use App\Entity\Comptability\Invoice;
use App\Entity\Comptability\Contact;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Comptability\CustomerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $name = null; 

    #[ORM\ManyToOne(inversedBy:'customers', cascade: ['persist', 'remove'])]
    private ?ChartOfAccounts $chartOfAccounts = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoice::class, cascade: ['persist'])]
    private Collection $invoices;
   
    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?AdministrativeIdentifier $administrativeIdentifier = null;

    #[ORM\ManyToMany(targetEntity: Contact::class, mappedBy: 'customer', cascade: ['persist'])]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Impression::class, cascade: ['persist'])]
    private Collection $impressions;

    #[ORM\Column( nullable: true)]
    private ?bool $impressionAccess = false;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: BankDetail::class, orphanRemoval: true)]
    private Collection $bankDetails;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?Supplier $supplier = null;

 

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->impressions = new ArrayCollection();
        $this->bankDetails = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChartOfAccounts(): ?ChartOfAccounts
    {
        return $this->chartOfAccounts;
    }

    public function setChartOfAccounts(?ChartOfAccounts $chartOfAccounts): self
    {
        $this->chartOfAccounts = $chartOfAccounts;

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
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getAdministrativeIdentifier(): ?AdministrativeIdentifier
    {
        return $this->administrativeIdentifier;
    }

    public function setAdministrativeIdentifier(?AdministrativeIdentifier $administrativeIdentifier): self
    {
        $this->administrativeIdentifier = $administrativeIdentifier;

        return $this;
    }


    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->addCustomer($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            $contact->removeCustomer($this);
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
            $impression->setCustomer($this);
        }

        return $this;
    }

    public function removeImpression(Impression $impression): self
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getCustomer() === $this) {
                $impression->setCustomer(null);
            }
        }

        return $this;
    }

    public function isImpressionAccess(): ?bool
    {
        return $this->impressionAccess;
    }

    public function setImpressionAccess(bool $impressionAccess): self
    {
        $this->impressionAccess = $impressionAccess;

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



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setCustomer(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getCustomer() !== $this) {
            $user->setCustomer($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, BankDetail>
     */
    public function getBankDetails(): Collection
    {
        return $this->bankDetails;
    }

    public function addBankDetail(BankDetail $bankDetail): self
    {
        if (!$this->bankDetails->contains($bankDetail)) {
            $this->bankDetails->add($bankDetail);
            $bankDetail->setCustomer($this);
        }

        return $this;
    }

    public function removeBankDetail(BankDetail $bankDetail): self
    {
        if ($this->bankDetails->removeElement($bankDetail)) {
            // set the owning side to null (unless already changed)
            if ($bankDetail->getCustomer() === $this) {
                $bankDetail->setCustomer(null);
            }
        }

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        // unset the owning side of the relation if necessary
        if ($supplier === null && $this->supplier !== null) {
            $this->supplier->setCustomer(null);
        }

        // set the owning side of the relation if necessary
        if ($supplier !== null && $supplier->getCustomer() !== $this) {
            $supplier->setCustomer($this);
        }

        $this->supplier = $supplier;

        return $this;
    }
}
