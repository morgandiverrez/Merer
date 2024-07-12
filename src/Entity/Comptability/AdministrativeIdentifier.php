<?php

namespace App\Entity\Comptability;

use Faker\Factory;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Comptability\AdministrativeIdentifierRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AdministrativeIdentifierRepository::class)]
#[UniqueEntity(fields: ['siret'], message: 'Il y a déjà un identifiant avec ce SIRET')]
#[UniqueEntity(fields: ['APE'], message: 'Il y a déjà un identifiant avec cette APE')]
class AdministrativeIdentifier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::BIGINT,unique: true, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 15,unique: true, nullable: true)]
    private ?string $APE = null;

    #[ORM\OneToMany(mappedBy: 'administrativeIdentifier', targetEntity: Institution::class)]
    private Collection $institutions;

    #[ORM\OneToMany(mappedBy: 'administrativeIdentifier', targetEntity: Customer::class)]
    private Collection $customers;

    #[ORM\OneToMany(mappedBy: 'administrativeIdentifier', targetEntity: Supplier::class)]
    private Collection $suppliers;

   

    public function __construct()
    {
        $this->institutions = new ArrayCollection();
        $this->customers = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->getSiret();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAPE(): ?string
    {
        return $this->APE;
    }

    public function setAPE(?string $APE): self
    {
        $this->APE = $APE;

        return $this;
    }

    /**
     * @return Collection<int, Institution>
     */
    public function getInstitutions(): Collection
    {
        return $this->institutions;
    }

    public function addInstitution(Institution $institution): self
    {
        if (!$this->institutions->contains($institution)) {
            $this->institutions->add($institution);
            $institution->setAdministrativeIdentifier($this);
        }

        return $this;
    }

    public function removeInstitution(Institution $institution): self
    {
        if ($this->institutions->removeElement($institution)) {
            // set the owning side to null (unless already changed)
            if ($institution->getAdministrativeIdentifier() === $this) {
                $institution->setAdministrativeIdentifier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setAdministrativeIdentifier($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getAdministrativeIdentifier() === $this) {
                $customer->setAdministrativeIdentifier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supplier>
     */
    public function getSuppliers(): Collection
    {
        return $this->suppliers;
    }

    public function addSupplier(Supplier $supplier): self
    {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
            $supplier->setAdministrativeIdentifier($this);
        }

        return $this;
    }

    public function removeSupplier(Supplier $supplier): self
    {
        if ($this->suppliers->removeElement($supplier)) {
            // set the owning side to null (unless already changed)
            if ($supplier->getAdministrativeIdentifier() === $this) {
                $supplier->setAdministrativeIdentifier(null);
            }
        }

        return $this;
    }

   
}
