<?php

namespace App\Entity\Formation;

use App\Entity\Formation\Lieu;
use App\Entity\Formation\Lieux;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Formation\EvenementRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà un événement avec ce nom')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateDebut;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateFin;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $URL;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: seance::class, cascade: ['persist', 'remove'])]
    private $seances;

    #[ORM\Column(type: 'array', nullable: true)]
    private $parcours = [];

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $autorisationPhoto;

    #[ORM\Column(type: 'array', nullable: true)]
    private $modePaiement = [];

    #[ORM\Column(type:'boolean', nullable: true)]
    private $covoiturage = false;

    #[ORM\Column(type:'boolean', nullable: true)]
    private $parcoursObligatoire = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $visible =false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateFinInscription;

    #[ORM\ManyToOne(targetEntity: lieux::class, inversedBy:'evenements', cascade: ['persist'])]
    private $lieu;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nombrePlace;



   
    public function __construct()
    {
        $this->seances = new ArrayCollection();
      
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

    public function setName(?string $name): self
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
    
    public function getURL(): ?string
    {
        return $this->URL;
    }

    public function setURL(?string $URL): self
    {
        $this->URL = $URL;

        return $this;
    }

    /**
     * @return Collection<int, seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances[] = $seance;
            $seance->setEvenement($this);
        }

        return $this;
    }

    public function removeSeance(seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getEvenement() === $this) {
                $seance->setEvenement(null);
            }
        }

        return $this;
    }

    public function getParcours(): ?array
    {
        return $this->parcours;
    }

    public function setParcours(?array $parcours): self
    {
        $this->parcours = $parcours;

        return $this;
    }

    public function addParcours(?string $parcours): self
    {
        $this->parcours =array_push($this->getParcours(),$parcours);

        return $this;
    }

    public function isAutorisationPhoto(): ?bool
    {
        return $this->autorisationPhoto;
    }

    public function setAutorisationPhoto(?bool $autorisationPhoto): self
    {
        $this->autorisationPhoto = $autorisationPhoto;

        return $this;
    }

    public function getModePaiement(): ?array
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?array $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function isCovoiturage(): ?bool
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(bool $covoiturage): self
    {
        $this->covoiturage = $covoiturage;

        return $this;
    }

    public function isParcoursObligatoire(): ?bool
    {
        return $this->parcoursObligatoire;
    }

    public function setParcoursObligatoire(bool $parcoursObligatoire): self
    {
        $this->parcoursObligatoire = $parcoursObligatoire;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->dateFinInscription;
    }

    public function setDateFinInscription(?\DateTimeInterface $dateFinInscription): self
    {
        $this->dateFinInscription = $dateFinInscription;

        return $this;
    }

    public function getLieu(): ?lieux
    {
        return $this->lieu;
    }

    public function setLieu(?lieux $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getNombrePlace(): ?int
    {
        return $this->nombrePlace;
    }

    public function setNombrePlace(?int $nombrePlace): self
    {
        $this->nombrePlace = $nombrePlace;

        return $this;
    }

}
