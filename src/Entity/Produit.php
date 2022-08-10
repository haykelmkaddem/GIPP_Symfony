<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @Groups("produit")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * @Groups("produit")
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @Groups("produit")
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @Groups("produit")
     * @ORM\Column(type="integer")
     */
    private $min;

    /**
     * @Groups("produit")
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @Groups("categorie_detail","pourproduit")
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="Produit")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $categorie;


    /**
     * @Groups("pourproduit")
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="produit")
     */
    private $panier;

    /**
     * @Groups("pourproduit")
     * @ORM\OneToMany(targetEntity=ProduitVendus::class, mappedBy="produit")
     */
    private $Produitvendus;

    /**
     * @Groups("forproductavis")
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="produit")
     */
    private $avis;

    /**
     * @Groups("produit")
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * @Groups("produit")
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="produit", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @Groups("produit")
     * @ORM\Column(type="integer")
     */
    private $vu;

    /**
     * @Groups("produit")
     * @ORM\Column(type="boolean")
     */
    private $visibilite;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=255)
     */
    private $nomAr;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=255)
     */
    private $nomIt;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=255)
     */
    private $nomEn;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=5000)
     */
    private $descriptionIt;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=5000)
     */
    private $descriptionAr;

    /**
     * @Groups("produit")
     * @ORM\Column(type="string", length=5000)
     */
    private $descriptionEn;

    public function __construct()
    {
        $this->panier = new ArrayCollection();
        $this->Produitvendus = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->image = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }


    /**
     * @return Collection<int, Panier>
     */
    public function getPanier(): Collection
    {
        return $this->panier;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->panier->contains($panier)) {
            $this->panier[] = $panier;
            $panier->setProduit($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->panier->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getProduit() === $this) {
                $panier->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProduitVendus>
     */
    public function getProduitvendus(): Collection
    {
        return $this->Produitvendus;
    }

    public function addProduitvendu(ProduitVendus $produitvendu): self
    {
        if (!$this->Produitvendus->contains($produitvendu)) {
            $this->Produitvendus[] = $produitvendu;
            $produitvendu->setProduit($this);
        }

        return $this;
    }

    public function removeProduitvendu(ProduitVendus $produitvendu): self
    {
        if ($this->Produitvendus->removeElement($produitvendu)) {
            // set the owning side to null (unless already changed)
            if ($produitvendu->getProduit() === $this) {
                $produitvendu->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setProduit($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getProduit() === $this) {
                $avi->setProduit(null);
            }
        }

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setProduit($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduit() === $this) {
                $image->setProduit(null);
            }
        }

        return $this;
    }

    public function getVu(): ?int
    {
        return $this->vu;
    }

    public function setVu(int $vu): self
    {
        $this->vu = $vu;

        return $this;
    }

    public function getVisibilite(): ?bool
    {
        return $this->visibilite;
    }

    public function setVisibilite(bool $visibilite): self
    {
        $this->visibilite = $visibilite;

        return $this;
    }

    public function getNomAr(): ?string
    {
        return $this->nomAr;
    }

    public function setNomAr(string $nomAr): self
    {
        $this->nomAr = $nomAr;

        return $this;
    }

    public function getNomIt(): ?string
    {
        return $this->nomIt;
    }

    public function setNomIt(string $nomIt): self
    {
        $this->nomIt = $nomIt;

        return $this;
    }

    public function getNomEn(): ?string
    {
        return $this->nomEn;
    }

    public function setNomEn(string $nomEn): self
    {
        $this->nomEn = $nomEn;

        return $this;
    }

    public function getDescriptionIt(): ?string
    {
        return $this->descriptionIt;
    }

    public function setDescriptionIt(string $descriptionIt): self
    {
        $this->descriptionIt = $descriptionIt;

        return $this;
    }

    public function getDescriptionAr(): ?string
    {
        return $this->descriptionAr;
    }

    public function setDescriptionAr(string $descriptionAr): self
    {
        $this->descriptionAr = $descriptionAr;

        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn): self
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }
}
