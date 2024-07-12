<?php

namespace App\Entity\Comptability;

use App\Repository\Comptability\FundTypeFundBoxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FundTypeFundBoxRepository::class)]
class FundTypeFundBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fundTypeFundBoxes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundType $fundType = null;

    #[ORM\ManyToOne(inversedBy: 'fundTypeFundBoxes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundBox $fundBox = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $Horrodateur;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFundType(): ?FundType
    {
        return $this->fundType;
    }

    public function setFundType(?FundType $fundType): self
    {
        $this->fundType = $fundType;

        return $this;
    }

    public function getHorrodateur(): ?\DateTimeInterface
    {
        return $this->Horrodateur;
    }

    public function setHorrodateur(?\DateTimeInterface $Horrodateur): self
    {
        $this->Horrodateur = $Horrodateur;

        return $this;
    }

    public function getFundBox(): ?FundBox
    {
        return $this->fundBox;
    }

    public function setFundBox(?FundBox $fundBox): self
    {
        $this->fundBox = $fundBox;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
