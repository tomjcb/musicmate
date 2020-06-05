<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbLikes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDislikes = 0;

    /**
     * @ORM\OneToMany(targetEntity=Signalement::class, mappedBy="publication")
     */
    private $signalements;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="publication")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Dislike::class, mappedBy="publication")
     */
    private $dislikes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $songlinked;


    public function __construct()
    {
        $this->signalements = new ArrayCollection();
        $this->userlike = new ArrayCollection();
        $this->userdislike = new ArrayCollection();
        $this->userlikes = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getNbLikes(): ?int
    {
        return $this->nbLikes;
    }

    public function setNbLikes(int $nbLikes): self
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    public function getNbDislikes(): ?int
    {
        return $this->nbDislikes;
    }

    public function setNbDislikes(int $nbDislikes): self
    {
        $this->nbDislikes = $nbDislikes;

        return $this;
    }

    /**
     * @return Collection|Signalement[]
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): self
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements[] = $signalement;
            $signalement->setPublication($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->contains($signalement)) {
            $this->signalements->removeElement($signalement);
            // set the owning side to null (unless already changed)
            if ($signalement->getPublication() === $this) {
                $signalement->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPublication($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getPublication() === $this) {
                $like->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Dislike[]
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addDislike(Dislike $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
            $dislike->setPublication($this);
        }

        return $this;
    }

    public function removeDislike(Dislike $dislike): self
    {
        if ($this->dislikes->contains($dislike)) {
            $this->dislikes->removeElement($dislike);
            // set the owning side to null (unless already changed)
            if ($dislike->getPublication() === $this) {
                $dislike->setPublication(null);
            }
        }

        return $this;
    }

    public function getSonglinked(): ?string
    {
        return $this->songlinked;
    }

    public function setSonglinked(?string $songlinked): self
    {
        $this->songlinked = $songlinked;

        return $this;
    }

}
