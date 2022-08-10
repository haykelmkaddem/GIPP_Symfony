<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @Groups("commande")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("commande")
     * @ORM\Column(type="string", length=255)
     */
    private $methode_de_paiement;

    /**
     * @Groups("commande")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @Groups("commande")
     * @ORM\Column(type="float")
     */
    private $totale;

    /**
     * @Groups("commande")
     * @ORM\Column(type="string", length=255)
     */
    private $statut_commande;

    /**
     * @Groups("commande")
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @Groups("commande")
     * @ORM\Column(type="datetime_immutable")
     */
    private $date_modification;

    /**
     * @Groups("commande")
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $reference;

    /**
     * @Groups("produitvendus")
     * @ORM\OneToMany(targetEntity=ProduitVendus::class, mappedBy="commande")
     */
    private $produitVendus;

    /**
     * @Groups("commande")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commande")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    public function __construct()
    {
        $this->produitVendus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethodeDePaiement(): ?string
    {
        return $this->methode_de_paiement;
    }

    public function setMethodeDePaiement(string $methode_de_paiement): self
    {
        $this->methode_de_paiement = $methode_de_paiement;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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

    public function getStatutCommande(): ?string
    {
        return $this->statut_commande;
    }

    public function setStatutCommande(string $statut_commande): self
    {
        $this->statut_commande = $statut_commande;

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

    public function getDateModification(): ?\DateTimeImmutable
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeImmutable $date_modification): self
    {
        $this->date_modification = $date_modification;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection<int, ProduitVendus>
     */
    public function getProduitVendus(): Collection
    {
        return $this->produitVendus;
    }

    public function addProduitVendu(ProduitVendus $produitVendu): self
    {
        if (!$this->produitVendus->contains($produitVendu)) {
            $this->produitVendus[] = $produitVendu;
            $produitVendu->setCommande($this);
        }

        return $this;
    }

    public function removeProduitVendu(ProduitVendus $produitVendu): self
    {
        if ($this->produitVendus->removeElement($produitVendu)) {
            // set the owning side to null (unless already changed)
            if ($produitVendu->getCommande() === $this) {
                $produitVendu->setCommande(null);
            }
        }

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
