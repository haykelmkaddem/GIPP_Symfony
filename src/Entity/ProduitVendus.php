<?php

namespace App\Entity;

use App\Repository\ProduitVendusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProduitVendusRepository::class)
 */
class ProduitVendus
{
    /**
     * @Groups("produitvendus")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("produitvendus")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("produitvendus")
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @Groups("produitvendus")
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @Groups("produitvendus")
     * @ORM\Column(type="float")
     */
    private $totale;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="produitVendus")
     */
    private $commande;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="Produitvendus")
     */
    private $produit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTotale(): ?float
    {
        return $this->totale;
    }

    public function setTotale(float $totale): self
    {
        $this->totale = $totale;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

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
}
