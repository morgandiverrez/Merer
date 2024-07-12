<?php

namespace App\Entity\Formation;

use App\Repository\Formation\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Il y a déjà une badge avec ce code')]
#[UniqueEntity(fields: ['name'], message: 'Il y a déjà une badge avec ce nom')]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255,unique: true, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\Column(type: 'string',unique: true, length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $categorie;

    #[ORM\Column(type: 'string', length: 2048, nullable: true)]
    private $description;

    #[ORM\Column(type: 'date', nullable: true)]
    private $date_creation;

    #[ORM\ManyToMany(targetEntity: Profil::class, cascade: ['persist'],inversedBy:'badge')]
    private $profil;


    #[ORM\OneToMany(mappedBy: 'badge', cascade: ['persist'], targetEntity: Formation::class)]
    private $formations;





    

    public function __construct()
    {
        $this->profil = new ArrayCollection();
        $this->formations = new ArrayCollection();
       
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @return Collection<int, profil>
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(profil $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
        }

        return $this;
    }

    public function removeProfil(profil $profil): self
    {
        $this->profil->removeElement($profil);

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->setBadge($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getBadge() === $this) {
                $formation->setBadge(null);
            }
        }

        return $this;
    }




}
