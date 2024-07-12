<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\FundBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FundBoxRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà une caisse avec ce nom')]
class FundBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $lastCountDate = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?ChartOfAccounts $chartOfAccounts = null;

    #[ORM\ManyToOne( cascade: ['persist'])]
    private ?Location $location = null;

    #[ORM\OneToMany(mappedBy: 'fundBox', targetEntity: FundTypeFundBox::class, orphanRemoval: true)]
    private Collection $fundTypeJoin;

    #[ORM\Column(unique: true, length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->fundTypeJoin = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->getName();
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

    public function getLastCountDate(): ?\DateTimeInterface
    {
        return $this->lastCountDate;
    }

    public function setLastCountDate(\DateTimeInterface $lastCountDate): self
    {
        $this->lastCountDate = $lastCountDate;

        return $this;
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }


    /**
     * @return Collection<int, FundTypeFundBox>
     */
    public function getFundTypeJoin(): Collection
    {
        return $this->fundTypeJoin;
    }

    public function addFundTypeJoin(FundTypeFundBox $fundTypeJoin): self
    {
        if (!$this->fundTypeJoin->contains($fundTypeJoin)) {
            $this->fundTypeJoin->add($fundTypeJoin);
            $fundTypeJoin->setFundBox($this);
        }

        return $this;
    }

    public function removeFundTypeJoin(FundTypeFundBox $fundTypeJoin): self
    {
        if ($this->fundTypeJoin->removeElement($fundTypeJoin)) {
            // set the owning side to null (unless already changed)
            if ($fundTypeJoin->getFundBox() === $this) {
                $fundTypeJoin->setFundBox(null);
            }
        }

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
}
