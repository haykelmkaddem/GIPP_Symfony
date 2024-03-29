<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\ProduitVendusRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/avis")
 */
class AvisController extends AbstractController
{
    /**
     * @Route("/", name="avis_index", methods={"GET"})
     */
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="avis_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $avi = new Avis();
            $avi->setEtoileNb($data['etoileNb']);
            $avi->setCommentaire($data['commentaire']);
            $avi->setCreatedAt(date_create_immutable('now'));
            $userdata = $userRepository->find($data['userId']);
            $avi->setUser($userdata);
            $produitdate = $produitRepository->find($data['produitId']);
            $avi->setProduit($produitdate);
            $entityManager->persist($avi);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($avi, 'json', ['groups' => ['avis','produit','user']]);
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
     * @Route("/show", name="avis_show", methods={"GET", "POST"})
     */
    public function show(Request $request, AvisRepository $avisRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $avisdate = $avisRepository->findOneBy(['id'=> $data['avisId']]);
            $dataRes = $this->get('serializer')->serialize($avisdate, 'json', ['groups' => ['avis','produit','user']]);
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
     * @Route("/edit", name="avis_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $avisdata = $avisRepository->find($data['avisId']);
            $avisdata->setEtoileNb($data['etoileNb']);
            $avisdata->setCommentaire($data['commentaire']);
            $entityManager->persist($avisdata);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($avisdata, 'json', ['groups' => ['avis','produit','user']]);
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
     * @Route("/delete", name="avis_delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $avisdata = $avisRepository->find($data['avisId']);
            $entityManager->remove($avisdata);
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
     * @Route("/showall", name="avis_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
    {
        $avisdata = $avisRepository->findAll();
            $dataRes = $this->get('serializer')->serialize($avisdata, 'json', ['groups' => ['avis','produit','user', 'entreprise','userEntreprise']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
    }

    /**
     * @Route("/avisofproduct", name="avis_avisofproduct", methods={"GET", "POST"})
     */
    public function avisofproduct(Request $request, AvisRepository $avisRepository, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {

            $produit = $produitRepository->findOneBy(['id'=> $data['produitId']]);
            $avis = $avisRepository->findBy(['produit'=> $produit]);

            $dataRes = $this->get('serializer')->serialize($avis, 'json', ['groups' => ['avis','produit','user', 'entreprise','userEntreprise']]);
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
     * @Route("/showAvisOrNot", name="avis_showAvisOrNot", methods={"GET", "POST"})
     */
    public function showAvisOrNot(Request $request, AvisRepository $avisRepository, ProduitRepository $produitRepository, UserRepository $userRepository, CommandeRepository$commandeRepository, ProduitVendusRepository $produitVendusRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {

            $user = $userRepository->findOneBy(['id'=> $data['userId']]);
            $produit = $produitRepository->findOneBy(['id'=> $data['produitId']]);
            $commandes = $commandeRepository->findBy(['user'=> $user]);
            $i = false;
            foreach ( $commandes as $commande){
                $vendusList = $produitVendusRepository->findBy(['commande'=> $commande]);
                foreach ($vendusList as $produitVendus){
                    if ($produitVendus->getProduit() == $produit){
                        $i = true;
                    }
                }
            }

            $dataRes = $this->get('serializer')->serialize($i, 'json', ['groups' => ['avis','produit','user']]);
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

}
