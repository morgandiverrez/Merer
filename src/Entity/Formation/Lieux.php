<?php

namespace App\Entity\Formation;

use App\Repository\Formation\LieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuxRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Il y a déjà un lieu avec ce code')]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà un lieu avec ce nom')]
class Lieux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $salle;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adresse;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $code_postale;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ville;

    #[ORM\ManyToMany(targetEntity: Seance::class, mappedBy: 'lieux', cascade: ['persist'])]
    private $seance;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: SeanceProfil::class, cascade: ['persist'])]
    private $seanceProfils;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Evenement::class, cascade: ['persist'])]
    private $evenements;


    public function __construct()
    {
        $this->seance = new ArrayCollection();
        $this->seanceProfils = new ArrayCollection();
        $this->evenements = new ArrayCollection();
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(?string $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostale(): ?string
    {
        return $this->code_postale;
    }

    public function setCodePostale(?string $code_postale): self
    {
        $this->code_postale = $code_postale;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeance(): Collection
    {
        return $this->seance;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seance->contains($seance)) {
            $this->seance[] = $seance;
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        $this->seance->removeElement($seance);

        return $this;
    }

    /**
     * @return Collection<int, SeanceProfil>
     */
    public function getSeanceProfils(): Collection
    {
        return $this->seanceProfils;
    }

    public function addSeanceProfil(SeanceProfil $seanceProfil): self
    {
        if (!$this->seanceProfils->contains($seanceProfil)) {
            $this->seanceProfils[] = $seanceProfil;
            $seanceProfil->setLieu($this);
        }

        return $this;
    }

    public function removeSeanceProfil(SeanceProfil $seanceProfil): self
    {
        if ($this->seanceProfils->removeElement($seanceProfil)) {
            // set the owning side to null (unless already changed)
            if ($seanceProfil->getLieu() === $this) {
                $seanceProfil->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setLieu($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getLieu() === $this) {
                $evenement->setLieu(null);
            }
        }

        return $this;
    }

 
}
