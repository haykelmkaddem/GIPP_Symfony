<?php

namespace App\Controller;

use App\Entity\Salon;
use App\Form\SalonType;
use App\Repository\SalonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/salon")
 */
class SalonController extends AbstractController
{
    /**
     * @Route("/", name="salon_index", methods={"GET"})
     */
    public function index(SalonRepository $salonRepository): Response
    {
        return $this->render('salon/index.html.twig', [
            'salons' => $salonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="salon_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $salon = new Salon();
            $salon->setTitre($data['titre']);
            $salon->setDescription($data['description']);
            $salon->setDate(new \DateTime($data['date']));
            $salon->setTempsDebut(new \DateTime($data['temps_debut']));
            $salon->setTempsFin(new \DateTime($data['temps_fin']));
            $salon->setLieu($data['lieu']);
            $salon->setMaxInvitation($data['max_invitation']);
            $entityManager->persist($salon);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($salon, 'json', ['groups' => ['salon','reservation','user']]);
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
     * @Route("/show", name="salon_show", methods={"GET", "POST"})
     */
    public function show(Request $request, SalonRepository $salonRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $salondata = $salonRepository->findOneBy(['id' => $data['salonId']]);
            $dataRes = $this->get('serializer')->serialize($salondata, 'json', ['groups' => ['salon','reservation','user']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $er = [
                'message' => 'pas de données'
            ];
            $dataRes = $this->get('serializer')->serialize($er, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/edit", name="salon_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, SalonRepository $salonRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $salondata = $salonRepository->findOneBy(['id' => $data['salonId']]);
            $salondata->setTitre($data['titre']);
            $salondata->setDescription($data['description']);
            $salondata->setDate(new \DateTime($data['date']));
            $salondata->setTempsDebut(new \DateTime($data['temps_debut']));
            $salondata->setTempsFin(new \DateTime($data['temps_fin']));
            $salondata->setLieu($data['lieu']);
            $salondata->setMaxInvitation($data['max_invitation']);

            $entityManager->persist($salondata);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($salondata, 'json', ['groups' => ['salon','reservation','user']]);
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
     * @Route("/delete", name="salon_delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, SalonRepository $salonRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $salondata = $salonRepository->findOneBy(['id' => $data['salonId']]);

            $entityManager->remove($salondata);
            $entityManager->flush();

            $res = [
                'message'=> "success"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else{
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
     * @Route("/showall", name="salon_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, SalonRepository $salonRepository): Response
    {
        $salondata = $salonRepository->findAll();
        if($salondata) {
            $dataRes = $this->get('serializer')->serialize($salondata, 'json', ['groups' => ['salon','reservation','user']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $categorydata = [
                'message' => 'pas de données'
            ];
            $dataRes = $this->get('serializer')->serialize($categorydata, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
