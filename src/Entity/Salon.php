<?php

namespace App\Entity;

use App\Repository\SalonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SalonRepository::class)
 */
class Salon
{
    /**
     * @Groups("salon","salonreservation")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="time")
     */
    private $temps_debut;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="time")
     */
    private $temps_fin;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;

    /**
     * @Groups("salon","salonreservation")
     * @ORM\Column(type="integer")
     */
    private $max_invitation;

    /**
     * @Groups("salon")
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="salon")
     */
    private $reservation;

    public function __construct()
    {
        $this->reservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTempsDebut(): ?\DateTimeInterface
    {
        return $this->temps_debut;
    }

    public function setTempsDebut(\DateTimeInterface $temps_debut): self
    {
        $this->temps_debut = $temps_debut;

        return $this;
    }

    public function getTempsFin(): ?\DateTimeInterface
    {
        return $this->temps_fin;
    }

    public function setTempsFin(\DateTimeInterface $temps_fin): self
    {
        $this->temps_fin = $temps_fin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getMaxInvitation(): ?int
    {
        return $this->max_invitation;
    }

    public function setMaxInvitation(int $max_invitation): self
    {
        $this->max_invitation = $max_invitation;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation[] = $reservation;
            $reservation->setSalon($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getSalon() === $this) {
                $reservation->setSalon(null);
            }
        }

        return $this;
    }
}
