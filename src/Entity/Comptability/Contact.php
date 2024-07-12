<?php

namespace App\Entity\Comptability;

use App\Entity\Comptability\Customer;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Comptability\ContactRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[UniqueEntity(fields: ['mail'], message: 'Il y a déjà une boite de cheque avec cette email')]
#[UniqueEntity(fields: ['phone'], message: 'Il y a déjà une boite de cheque avec ce numéro de téléphone')]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length:255, unique: true, nullable: true)]
    private ?string $mail = null;

    #[ORM\Column(nullable: true, unique: true)]
    private ?string $phone = null;

    #[ORM\ManyToMany(targetEntity: Customer::class, inversedBy: 'contacts', cascade: ['persist'])]
    private Collection $customer;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $civility = null;

    #[ORM\ManyToOne]
    private ?Location $Location = null;

   #[ORM\ManyToMany(targetEntity: Supplier::class, cascade: ['persist'], inversedBy: 'contacts')]
    private Collection $supplier;


    public function __construct()
    {
        $this->customer = new ArrayCollection();
        $this->supplier = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomer(): Collection
    {
        return $this->customer;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customer->contains($customer)) {
            $this->customer->add($customer);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        $this->customer->removeElement($customer);

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->Location;
    }

    public function setLocation(?Location $Location): self
    {
        $this->Location = $Location;

        return $this;
    }

  


    /**
     * @return Collection<int, Supplier>
     */
    public function getSupplier(): Collection
    {
        return $this->supplier;
    }

    public function addSupplier(Supplier $supplier): self
    {
        if (!$this->supplier->contains($supplier)) {
            $this->supplier->add($supplier);
            $supplier->addContact($this);
        }

        return $this;
    }

    public function removeSupplier(Supplier $supplier): self
    {
         $this->supplier->removeElement($supplier);


        return $this;
    }
}
