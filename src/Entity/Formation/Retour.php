<?php

namespace App\Entity\Formation;

use App\Repository\Formation\RetourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourRepository::class)]
class Retour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer' )]
    private $note_contenu;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_contenu;

    #[ORM\Column(type: 'integer')]
    private $note_animation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_animation;

    #[ORM\Column(type: 'integer')]
    private $note_implication;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_implication;

    #[ORM\Column(type: 'integer')]
    private $note_reponse_atente;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_reponse_attente;

    #[ORM\Column(type: 'integer')]
    private $note_niv_competence;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_niv_competence;

    #[ORM\Column(type: 'integer')]
    private $note_utilite;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_utilite;

    #[ORM\Column(type: 'integer')]
    private $note_generale;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $remarque_generale;

    #[ORM\Column(type: 'string', length:255, nullable: true)]
    private $apport_generale;

    #[ORM\Column(type: 'string', length:255, nullable: true)]
    private $plus_aimer;

    #[ORM\Column(type: 'string', length:255, nullable: true)]
    private $moins_aimer;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $aimer_voir;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $mot_fin;

    #[ORM\ManyToOne(targetEntity: Seance::class, inversedBy: 'retour', cascade: ['persist'])]
    private $seance;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'retour', cascade: ['persist'])]
    private $profil;


    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getNoteContenu(): ?int
    {
        return $this->note_contenu;
    }

    public function setNoteContenu(?int $note_contenu): self
    {
        $this->note_contenu = $note_contenu;

        return $this;
    }

    public function getRemarqueContenu(): ?string
    {
        return $this->remarque_contenu;
    }

    public function setRemarqueContenu(?string $remarque_contenu): self
    {
        $this->remarque_contenu = $remarque_contenu;

        return $this;
    }

    public function getNoteAnimation(): ?int
    {
        return $this->note_animation;
    }

    public function setNoteAnimation(?int $note_animation): self
    {
        $this->note_animation = $note_animation;

        return $this;
    }

    public function getRemarqueAnimation(): ?string
    {
        return $this->remarque_animation;
    }

    public function setRemarqueAnimation(?string $remarque_animation): self
    {
        $this->remarque_animation = $remarque_animation;

        return $this;
    }

    public function getNoteImplication(): ?string
    {
        return $this->note_implication;
    }

    public function setNoteImplication(?string $note_implication): self
    {
        $this->note_implication = $note_implication;

        return $this;
    }

    public function getRemarqueImplication(): ?string
    {
        return $this->remarque_implication;
    }

    public function setRemarqueImplication(?string $remarque_implication): self
    {
        $this->remarque_implication = $remarque_implication;

        return $this;
    }

    public function getNoteReponseAtente(): ?int
    {
        return $this->note_reponse_atente;
    }

    public function setNoteReponseAtente(?int $note_reponse_atente): self
    {
        $this->note_reponse_atente = $note_reponse_atente;

        return $this;
    }

    public function getRemarqueReponseAttente(): ?string
    {
        return $this->remarque_reponse_attente;
    }

    public function setRemarqueReponseAttente(?string $remarque_reponse_attente): self
    {
        $this->remarque_reponse_attente = $remarque_reponse_attente;

        return $this;
    }

    public function getNoteNivCompetence(): ?int
    {
        return $this->note_niv_competence;
    }

    public function setNoteNivCompetence(?int $note_niv_competence): self
    {
        $this->note_niv_competence = $note_niv_competence;

        return $this;
    }

    public function getRemarqueNivCompetence(): ?string
    {
        return $this->remarque_niv_competence;
    }

    public function setRemarqueNivCompetence(?string $remarque_niv_competence): self
    {
        $this->remarque_niv_competence = $remarque_niv_competence;

        return $this;
    }

    public function getNoteUtilite(): ?int
    {
        return $this->note_utilite;
    }

    public function setNoteUtilite(?int $note_utilite): self
    {
        $this->note_utilite = $note_utilite;

        return $this;
    }

    public function getRemarqueUtilite(): ?string
    {
        return $this->remarque_utilite;
    }

    public function setRemarqueUtilite(?string $remarque_utilite): self
    {
        $this->remarque_utilite = $remarque_utilite;

        return $this;
    }

    public function getNoteGenerale(): ?int
    {
        return $this->note_generale;
    }

    public function setNoteGenerale(?int $note_generale): self
    {
        $this->note_generale = $note_generale;

        return $this;
    }

    public function getRemarqueGenerale(): ?string
    {
        return $this->remarque_generale;
    }

    public function setRemarqueGenerale(?string $remarque_generale): self
    {
        $this->remarque_generale = $remarque_generale;

        return $this;
    }

    public function getApportGenerale(): ?string
    {
        return $this->apport_generale;
    }

    public function setApportGenerale(?string $apport_generale): self
    {
        $this->apport_generale = $apport_generale;

        return $this;
    }

    public function getPlusAimer(): ?string
    {
        return $this->plus_aimer;
    }

    public function setPlusAimer(?string $plus_aimer): self
    {
        $this->plus_aimer = $plus_aimer;

        return $this;
    }

    public function getMoinsAimer(): ?string
    {
        return $this->moins_aimer;
    }

    public function setMoinsAimer(?string $moins_aimer): self
    {
        $this->moins_aimer = $moins_aimer;

        return $this;
    }

    public function getAimerVoir(): ?string
    {
        return $this->aimer_voir;
    }

    public function setAimerVoir(?string $aimer_voir): self
    {
        $this->aimer_voir = $aimer_voir;

        return $this;
    }

    public function getMotFin(): ?string
    {
        return $this->mot_fin;
    }

    public function setMotFin(?string $mot_fin): self
    {
        $this->mot_fin = $mot_fin;

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
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

    
}
