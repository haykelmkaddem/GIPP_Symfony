<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @Groups("reservation","reservationR")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("reservation","reservationR")
     * @ORM\Column(type="string", length=255)
     */
    private $statut_reservation;

    /**
     * @Groups("reservationR")
     * @ORM\ManyToOne(targetEntity=Salon::class, inversedBy="reservation")
     */
    private $salon;

    /**
     * @Groups("reservation","reservationR")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservation")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatutReservation(): ?string
    {
        return $this->statut_reservation;
    }

    public function setStatutReservation(string $statut_reservation): self
    {
        $this->statut_reservation = $statut_reservation;

        return $this;
    }

    public function getSalon(): ?Salon
    {
        return $this->salon;
    }

    public function setSalon(?Salon $salon): self
    {
        $this->salon = $salon;

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
