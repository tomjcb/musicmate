<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeRepository::class)
 */
class Demande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fromuser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="demandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $touser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromuser(): ?string
    {
        return $this->fromuser;
    }

    public function setFromuser(string $fromuser): self
    {
        $this->fromuser = $fromuser;

        return $this;
    }

    public function getTouser(): ?User
    {
        return $this->touser;
    }

    public function setTouser(?User $touser): self
    {
        $this->touser = $touser;

        return $this;
    }
}
