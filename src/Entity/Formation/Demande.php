<?php

namespace App\Entity\Formation;

use App\Entity\Formation\Profil;
use App\Entity\Formation\EquipeElu;
use App\Entity\Formation\Formation;
use App\Entity\Formation\Association;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Formation\DemandeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'demandes', cascade: ['persist'])]
    private $profil;

    #[ORM\ManyToMany(targetEntity: Association::class, inversedBy:'demandes', cascade: ['persist'])]
    private $association;

    #[ORM\ManyToMany(targetEntity: EquipeElu::class, inversedBy:'demandes', cascade: ['persist'])]
    private $equipeElu;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateDebut;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateFin;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nombrePersonne;

    #[ORM\ManyToMany(targetEntity: Formation::class, inversedBy: 'demandes',  cascade: ['persist'])]
    private $formation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $planning;

    #[ORM\Column(type: 'boolean')]
    private $doubleMaillage = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    public function __construct()
    {
        $this->association = new ArrayCollection();
        $this->equipeElu = new ArrayCollection();
        $this->formation = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->getName();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection<int, association>
     */
    public function getAssociation(): Collection
    {
        return $this->association;
    }

    public function addAssociation(Association $association): self
    {
        if (!$this->association->contains($association)) {
            $this->association[] = $association;
        }

        return $this;
    }

    public function removeAssociation(Association $association): self
    {
        $this->association->removeElement($association);

        return $this;
    }

    /**
     * @return Collection<int, equipeElu>
     */
    public function getEquipeElu(): Collection
    {
        return $this->equipeElu;
    }

    public function addEquipeElu(EquipeElu $equipeElu): self
    {
        if (!$this->equipeElu->contains($equipeElu)) {
            $this->equipeElu[] = $equipeElu;
        }

        return $this;
    }

    public function removeEquipeElu(EquipeElu $equipeElu): self
    {
        $this->equipeElu->removeElement($equipeElu);

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNombrePersonne(): ?int
    {
        return $this->nombrePersonne;
    }

    public function setNombrePersonne(?int $nombrePersonne): self
    {
        $this->nombrePersonne = $nombrePersonne;

        return $this;
    }

    /**
     * @return Collection<int, formation>
     */
    public function getFormation(): Collection
    {
        return $this->formation;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formation->contains($formation)) {
            $this->formation[] = $formation;
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        $this->formation->removeElement($formation);

        return $this;
    }

    public function getPlanning(): ?string
    {
        return $this->planning;
    }

    public function setPlanning(?string $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    public function isDoubleMaillage(): ?bool
    {
        return $this->doubleMaillage;
    }

    public function setDoubleMaillage(bool $doubleMaillage): self
    {
        $this->doubleMaillage = $doubleMaillage;

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
}
