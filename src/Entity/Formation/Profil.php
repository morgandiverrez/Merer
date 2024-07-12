<?php

namespace App\Entity\Formation;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use App\Entity\Formation\Badge;
use App\Entity\Formation\Retour;
use App\Entity\Formation\Seance;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Formation\Demande;
use App\Entity\Formation\EquipeElu;
use App\Repository\Formation\ProfilRepository;
use App\Entity\Formation\Association;
use App\Entity\Formation\SeanceProfil;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[UniqueEntity(fields: ['telephone'], message: 'Il y a déjà un profil avec ce numéro de téléphone')]

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', unique: true, length: 255, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $last_name;

    #[ORM\Column(type: 'string',  length: 255, nullable: true)]
    private $telephone;
    
    #[ORM\Column(type: 'string', length: 255)]
    private $pronom;

    #[ORM\Column(type: 'date')]
    private $date_of_birth;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Retour::class, orphanRemoval:true)]
    private $retour;

    #[ORM\ManyToMany(targetEntity: EquipeElu::class, mappedBy: 'profil',cascade: ['persist'])]
    private $equipeElu;

    #[ORM\ManyToMany(targetEntity: Association::class, mappedBy: 'profil', cascade: ['persist'])]
    private $association;

    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'profil', cascade: ['persist'])]
    private $badge;

    #[ORM\ManyToMany(targetEntity: Seance::class, inversedBy: 'profil', cascade: ['persist'])]
    private $seance;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: SeanceProfil::class, orphanRemoval:true)]
    private $seanceProfil;

    #[ORM\OneToOne(mappedBy: 'profil', targetEntity: User::class, cascade: ['persist'])]
    private $user;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Demande::class, cascade: ['persist'])]
    private $demandes;


  
    public function __construct()
    {
        $this->retour = new ArrayCollection();
        $this->equipeElu = new ArrayCollection();
        $this->association = new ArrayCollection();
        $this->badge = new ArrayCollection();
        $this->seance = new ArrayCollection();
        $this->seanceProfil = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function  __toString()
    {
        $stringName = $this->getName().' '.$this->getLastName();
        return $stringName;
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

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPronom(): ?string
    {
        return $this->pronom;
    }

    public function setPronom(?string $pronom): self
    {
        $this->pronom = $pronom;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTimeInterface $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;

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
            $retour->setProfil($this);
        }

        return $this;
    }

    public function removeRetour(Retour $retour): self
    {
        if ($this->retour->removeElement($retour)) {
            // set the owning side to null (unless already changed)
            if ($retour->getProfil() === $this) {
                $retour->setProfil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EquipeElu>
     */
    public function getEquipeElu(): Collection
    {
        return $this->equipeElu;
    }

    public function addEquipeElu(EquipeElu $equipeElu): self
    {
        if (!$this->equipeElu->contains($equipeElu)) {
            $this->equipeElu[] = $equipeElu;
            $equipeElu->addProfil($this);
        }

        return $this;
    }

    public function removeEquipeElu(EquipeElu $equipeElu): self
    {
        if ($this->equipeElu->removeElement($equipeElu)) {
            $equipeElu->removeProfil($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Association>
     */
    public function getAssociation(): Collection
    {
        return $this->association;
    }

    public function addAssociation(Association $association): self
    {
        if (!$this->association->contains($association)) {
            $this->association[] = $association;
            $association->addProfil($this);
        }

        return $this;
    }

    public function removeAssociation(Association $association): self
    {
        if ($this->association->removeElement($association)) {
            $association->removeProfil($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadge(): Collection
    {
        return $this->badge;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badge->contains($badge)) {
            $this->badge[] = $badge;
            $badge->addProfil($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        if ($this->badge->removeElement($badge)) {
            $badge->removeProfil($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, seance>
     */
    public function getSeance(): Collection
    {
        return $this->seance;
    }

    public function addSeance(seance $seance): self
    {
        if (!$this->seance->contains($seance)) {
            $this->seance[] = $seance;
        }

        return $this;
    }

    public function removeSeance(seance $seance): self
    {
        $this->seance->removeElement($seance);

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
            $seanceProfil->setProfil($this);
        }

        return $this;
    }

    public function removeSeanceProfil(SeanceProfil $seanceProfil): self
    {
        if ($this->seanceProfil->removeElement($seanceProfil)) {
            // set the owning side to null (unless already changed)
            if ($seanceProfil->getProfil() === $this) {
                $seanceProfil->setProfil(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getProfil() !== $this) {
            $user->setProfil($this);
        }

        $this->user = $user;

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
            $demande->setProfil($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getProfil() === $this) {
                $demande->setProfil(null);
            }
        }

        return $this;
    }
 
    
}
