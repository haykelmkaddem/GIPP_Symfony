<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
{
    /**
     * @Groups("avis","avisforproduct")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("avis", "avisforproduct")
     * @ORM\Column(type="integer")
     */
    private $etoile_nb;

    /**
     * @Groups("avis", "avisforproduct")
     * @ORM\Column(type="string", length=3000)
     */
    private $commentaire;

    /**
     * @Groups("avis", "avisforproduct")
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @Groups("avis")
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="avis")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $produit;

    /**
     * @Groups("avis")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="avis")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtoileNb(): ?int
    {
        return $this->etoile_nb;
    }

    public function setEtoileNb(int $etoile_nb): self
    {
        $this->etoile_nb = $etoile_nb;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
