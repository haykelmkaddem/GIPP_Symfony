<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\SalonRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, SalonRepository $salonRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $reservation = new Reservation();
            $reservation->setStatutReservation($data['statut_reservation']);
            $userdata = $userRepository->find($data['userId']);
            $reservation->setUser($userdata);
            $salondata = $salonRepository->find($data['salonId']);
            $reservation->setSalon($salondata);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($reservation, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/show", name="reservation_show", methods={"GET", "POST"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/edit", name="reservation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $reservationData = $reservationRepository->findOneBy(['id'=>$data['reservationId']]);
            $reservationData->setStatutReservation($data['statut_reservation']);

            $entityManager->persist($reservationData);
            $entityManager->flush();
            $dataRes = $this->get('serializer')->serialize($reservationData, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    /**
     * @Route("/annuler", name="reservation_annuler", methods={"GET", "POST"})
     */
    public function annuler(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $reservationData = $reservationRepository->findOneBy(['id'=>$data['reservationId']]);
            $reservationData->setStatutReservation('Annulée');

            $entityManager->persist($reservationData);
            $entityManager->flush();
            $dataRes = $this->get('serializer')->serialize($reservationData, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    /**
     * @Route("/delete", name="reservation_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $reservationData = $reservationRepository->findOneBy(['id'=>$data['reservationId']]);
            $entityManager->remove($reservationData);
            $entityManager->flush();

            $res = [
                'message'=> "success"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/showall", name="reservation_delete", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->findAll();
        $dataRes = $this->get('serializer')->serialize($reservation, 'json', ['groups' => ['reservationR','user','salonreservation']]);
        $response = new Response($dataRes);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/showResParSalon", name="reservation_showResParSalon", methods={"GET", "POST"})
     */
    public function showResParSalon(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, SalonRepository $salonRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $salon = $salonRepository->findOneBy(['id'=>$data['salonId']]);
            $listReservation = $reservationRepository->findBy(['salon'=>$salon]);
            $dataRes = $this->get('serializer')->serialize($listReservation, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/showResParUser", name="reservation_showResParUser", methods={"GET", "POST"})
     */
    public function showResParUser(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, UserRepository $userRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);
            $listReservation = $reservationRepository->findBy(['user'=>$user]);
            $dataRes = $this->get('serializer')->serialize($listReservation, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/verifierUserSalonforComment", name="reservation_verifierUserSalonforComment", methods={"GET", "POST"})
     */
    public function verifierUserSalonforComment(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, UserRepository $userRepository, SalonRepository $salonRepository): Response
    {
        if ($data = json_decode($request->getContent(), true)) {
            $verif = false;
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);
            $salon = $salonRepository->findOneBy(['id'=>$data['salonId']]);
            $listreservation = $reservationRepository->findAll();
            foreach ($listreservation as $reservation){
                if($reservation->getSalon() == $salon && $reservation->getUser() == $user && $reservation->getStatutReservation() == "Accepté") {
                    $verif = true;
                }
            }
            $dataRes = $this->get('serializer')->serialize($verif, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    /**
     * @Route("/verifJustOneSalonReservationforEveryUser", name="reservation_verifJustOneSalonReservationforEveryUser", methods={"GET", "POST"})
     */
    public function verifJustOneSalonReservationforEveryUser(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, UserRepository $userRepository, SalonRepository $salonRepository): Response
    {
        if ($data = json_decode($request->getContent(), true)) {
            $verif = false;
            $user = $userRepository->findOneBy(['id' => $data['userId']]);
            $salon = $salonRepository->findOneBy(['id' => $data['salonId']]);
            $listReservation = $reservationRepository->findAll();
            foreach ($listReservation as $reservation) {
                if ($reservation->getSalon() == $salon && $reservation->getUser() == $user) {
                    $verif = true;
                }
            }
            $dataRes = $this->get('serializer')->serialize($verif, 'json', ['groups' => ['reservationR','user','salonreservation']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $res = [
                'message'=> "error"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
