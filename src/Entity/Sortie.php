<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Le nom de la sortie est obligatoire.')]
    #[Assert\NotNull(message: 'Le nom de la sortie est obligatoire.')]
    private ?string $nomSortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('today', message:'La date de création d\'un évènement est à J+1.')]
    #[Assert\NotBlank(message: 'La date de la sortie est obligatoire.')]
    #[Assert\NotNull(message: 'La date de la sortie est obligatoire.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Expression('this.getDateCloture() < this.getDateDebut()', message: 'La date limite d\'inscription ne peut être après la date de début de la sortie')]
    #[Assert\NotBlank(message: 'La date de clôture d\'inscriptions est obligatoire.')]
    #[Assert\NotNull(message: 'La date de clôture d\'inscriptions est obligatoire.')]
    private ?\DateTimeInterface $dateCloture = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le nombre maximum de participants est obligatoire.')]
    #[Assert\NotNull(message: 'Le nombre maximum de participants est obligatoire.')]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detailSortie = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $urlPhoto = null;

    #[ORM\ManyToOne(inversedBy: 'organisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organisateur = null;

    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'inscription')]
    private Collection $participants;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $raisonAbandon;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private ?bool $publie = null;

    /**
     * @return bool|null
     */
    public function getPublie(): ?bool
    {
        return $this->publie;
    }

    /**
     * @param bool|null $publie
     */
    public function setPublie(?bool $publie): void
    {
        $this->publie = $publie;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(string $nomSortie): self
    {
        $this->nomSortie = $nomSortie;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeInterface $dateCloture): self
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDetailSortie(): ?string
    {
        return $this->detailSortie;
    }

    public function setDetailSortie(?string $detailSortie): self
    {
        $this->detailSortie = $detailSortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }



    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipants(Participant $participants): self
    {
        if (!$this->participants->contains($participants)) {
            $this->participants->add($participants);
            $participants->addInscription($this);
        }

        return $this;
    }

    public function removeParticipants(Participant $participants): self
    {
        if ($this->participants->removeElement($participants)) {
            $participants->removeInscription($this);
        }
        return $this;
    }

    public function getRaisonAbandon(): ?string
    {
        return $this->raisonAbandon;
    }

    public function setRaisonAbandon(?string $raisonAbandon): self
    {
        $this->raisonAbandon = $raisonAbandon;

        return $this;
    }
}
