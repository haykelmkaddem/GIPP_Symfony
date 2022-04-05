<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EntrepriseRepository::class)
 */
class Entreprise
{
    /**
     * @Groups("entreprise")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $pays;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $code_postal;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=5000)
     */
    private $document_de_reference;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $lat;

    /**
     * @Groups("entreprise")
     * @ORM\Column(type="string", length=255)
     */
    private $lng;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="entreprise", cascade={"persist", "remove"})
     */
    private $user;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getDocumentDeReference(): ?string
    {
        return $this->document_de_reference;
    }

    public function setDocumentDeReference(string $document_de_reference): self
    {
        $this->document_de_reference = $document_de_reference;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }
}
