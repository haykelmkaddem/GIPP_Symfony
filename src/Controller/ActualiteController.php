<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/actualite")
 */
class ActualiteController extends AbstractController
{
    /**
     * @Route("/", name="actualite_index", methods={"GET"})
     */
    public function index(ActualiteRepository $actualiteRepository): Response
    {
        return $this->render('actualite/index.html.twig', [
            'actualites' => $actualiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="actualite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $actualite = new Actualite();
            $actualite->setTitre($data['titre']);
            $actualite->setDescription($data['description']);
            $actualite->setCreatedAt(date_create_immutable('now'));
            $actualite->setVisibilite(0);
            $entityManager->persist($actualite);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($actualite, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }else{
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
     * @Route("/show", name="actualite_show", methods={"GET", "POST"})
     */
    public function show(Request $request, ActualiteRepository $actualiteRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $actualitedata = $actualiteRepository->findOneBy(['id' => $data['actualiteId']]);
            $dataRes = $this->get('serializer')->serialize($actualitedata, 'json');
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
     * @Route("/edit", name="actualite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, ActualiteRepository $actualiteRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $actualite = $actualiteRepository->find($data['actualiteId']);
            $actualite->setTitre($data['titre']);
            $actualite->setDescription($data['description']);
            $entityManager->persist($actualite);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($actualite, 'json');
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
     * @Route("/delete", name="actualite_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ActualiteRepository  $actualiteRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $actualite = $actualiteRepository->find($data['actualiteId']);
            $entityManager->remove($actualite);
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
     * @Route("/showall", name="actualite_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, ActualiteRepository $actualiteRepository): Response
    {
        $actualitedata = $actualiteRepository->findAll();
        if($actualitedata) {
            $dataRes = $this->get('serializer')->serialize($actualitedata, 'json');
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
