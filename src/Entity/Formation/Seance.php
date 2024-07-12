<?php

namespace App\Entity\Formation;

use App\Entity\Formation\Profil;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Formation\SeanceRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Il y a déjà une séance avec ce code')]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $datetime;

    #[ORM\Column(type: 'integer')]
    private $nombreplace;

    #[ORM\Column(type: 'boolean')]
    private $visible = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $parcours;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'seance', cascade: ['persist'])]
    private $formation;

    #[ORM\ManyToMany(targetEntity: Lieux::class, inversedBy: 'seance', cascade: ['persist'])]
    private $lieux;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Retour::class, orphanRemoval:true)]
    private $retour;

    #[ORM\ManyToMany(targetEntity: Profil::class, mappedBy: 'seance', cascade: ['persist'])]
    private $profil;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: SeanceProfil::class, orphanRemoval: true)]
    private $seanceProfil;

  
    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'seances', cascade: ['persist'])]
    private $evenement;

    public function __construct()
    {
        $this->lieux = new ArrayCollection();
        $this->retour = new ArrayCollection();
        $this->profil = new ArrayCollection();
        $this->seanceProfil = new ArrayCollection();
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

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getNombreplace(): ?int
    {
        return $this->nombreplace;
    }

    public function setNombreplace(?int $nombreplace): self
    {
        $this->nombreplace = $nombreplace;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection<int, Lieux>
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(Lieux $lieux): self
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux[] = $lieux;
            $lieux->addSeance($this);
        }

        return $this;
    }

    public function removeLieux(Lieux $lieux): self
    {
        if ($this->lieux->removeElement($lieux)) {
            $lieux->removeSeance($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Retour>
     */
    public function getRetour(): Collection
    {
        return $this->retour;
    }

    public function addRetour(Retour $retour): self
    {
        if (!$this->retour->contains($retour)) {
            $this->retour[] = $retour;
            $retour->setSeance($this);
        }

        return $this;
    }

    public function removeRetour(Retour $retour): self
    {
        if ($this->retour->removeElement($retour)) {
            // set the owning side to null (unless already changed)
            if ($retour->getSeance() === $this) {
                $retour->setSeance(null);
            }
        }

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
            $profil->addSeance($this);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profil->removeElement($profil)) {
            $profil->removeSeance($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, SeanceProfil>
     */
    public function getSeanceProfil(): Collection
    {
        return $this->seanceProfil;
    }

    public function addSeanceProfil(SeanceProfil $seanceProfil): self
    {
        if (!$this->seanceProfil->contains($seanceProfil)) {
            $this->seanceProfil[] = $seanceProfil;
            $seanceProfil->setSeance($this);
        }

        return $this;
    }

    public function removeSeanceProfil(SeanceProfil $seanceProfil): self
    {
        if ($this->seanceProfil->removeElement($seanceProfil)) {
            // set the owning side to null (unless already changed)
            if ($seanceProfil->getSeance() === $this) {
                $seanceProfil->setSeance(null);
            }
        }

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

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function getParcours(): ?string
    {
        return $this->parcours;
    }

    public function setParcours(?string $parcours): self
    {
        $this->parcours = $parcours;

        return $this;
    }
}
