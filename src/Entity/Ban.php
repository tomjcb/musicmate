<?php

namespace App\Entity;

use App\Repository\BanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BanRepository::class)
 */
class Ban
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userbanned;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startBan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endBan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserbanned(): ?User
    {
        return $this->userbanned;
    }

    public function setUserbanned(User $userbanned): self
    {
        $this->userbanned = $userbanned;

        return $this;
    }

    public function getStartBan(): ?\DateTimeInterface
    {
        return $this->startBan;
    }

    public function setStartBan(\DateTimeInterface $startBan): self
    {
        $this->startBan = $startBan;

        return $this;
    }

    public function getEndBan(): ?\DateTimeInterface
    {
        return $this->endBan;
    }

    public function setEndBan(\DateTimeInterface $endBan): self
    {
        $this->endBan = $endBan;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
