<?php

namespace App\Entity\Formation;

use App\Repository\Formation\EquipeEluRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeEluRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Il y a déjà une équipe d\'elu avec ce code')]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà une équipe d\'elu avec ce nom')]
#[UniqueEntity(fields: ['adresse_mail'], message: 'Il y a déjà une équipe d\'elu avec cette email')]

class EquipeElu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string',unique: true, length:255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length:512, nullable: true)]
    private $categorie;

    #[ORM\Column(type: 'string', length: 2048, nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $adresse_mail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $etablissement;

    #[ORM\ManyToMany(targetEntity: Profil::class, inversedBy: 'equipeElu', cascade: ['persist'])]
    private $profil;

    #[ORM\Column(type: 'date', nullable: true)]
    private $date_election;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duree_mandat;

    #[ORM\Column(type: 'string', length: 512,  nullable: true)]
    private $fede_filliere ;

    #[ORM\ManyToMany(targetEntity: Demande::class, mappedBy:'equipeElu', cascade: ['persist'])]
    private $demandes;

    public function __construct()
    {
        $this->profil = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function __toString()
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

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

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

    public function getAdresseMail(): ?string
    {
        return $this->adresse_mail;
    }

    public function setAdresseMail(?string $adresse_mail): self
    {
        $this->adresse_mail = $adresse_mail;

        return $this;
    }

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(?string $etablissement): self
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    /**
     * @return Collection<int, Profil>
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        $this->profil->removeElement($profil);

        return $this;
    }

    public function getDateElection(): ?\DateTimeInterface
    {
        return $this->date_election;
    }

    public function setDateElection(?\DateTimeInterface $date_election): self
    {
        $this->date_election = $date_election;

        return $this;
    }

    public function getDureeMandat(): ?int
    {
        return $this->duree_mandat;
    }

    public function setDureeMandat(?int $duree_mandat): self
    {
        $this->duree_mandat = $duree_mandat;

        return $this;
    }

    public function getFedeFilliere(): ?string
    {
        return $this->fede_filliere;
    }

    public function setFedeFilliere(?string $fede_filliere): self
    {
        $this->fede_filliere = $fede_filliere;

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->addEquipeElu($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            $demande->removeEquipeElu($this);
        }

        return $this;
    }

    
}
