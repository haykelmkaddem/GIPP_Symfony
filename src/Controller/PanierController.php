<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier_index", methods={"GET"})
     */
    public function index(PanierRepository $panierRepository): Response
    {
        return $this->render('panier/index.html.twig', [
            'paniers' => $panierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="panier_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ProduitRepository $produitRepository, PanierRepository $panierRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->find($data['userId']);
            $produit = $produitRepository->find($data['produitId']);
            $listpanieruser = $panierRepository->findBy(['user'=>$user]);
            $test = false;

            foreach ($listpanieruser as $panier){
                if ($panier->getProduit() === $produit){
                    if (($panier->getQuantite() + $data['quantite'])<$produit->getMax() and ($panier->getQuantite() + $data['quantite']) > $produit->getMin()){
                        $panier->setQuantite($panier->getQuantite() + $data['quantite']);
                    } elseif (($panier->getQuantite() + $data['quantite'])>=$produit->getMax()) {
                        $panier->setQuantite($produit->getMax());
                    } elseif (($panier->getQuantite() + $data['quantite']) <= $produit->getMin()){
                        $panier->setQuantite($produit->getMin());
                    }
                    $entityManager->persist($panier);
                    $entityManager->flush();
                    $test = true;
                }
            }
            if (!$test){
                $panier = new Panier();
                if ($data['quantite'] < $produit->getMax() and $data['quantite'] > $produit->getMin()){
                    $panier->setQuantite($data['quantite']);
                } elseif ($data['quantite'] >= $produit->getMax()){
                    $panier->setQuantite($produit->getMax());
                } elseif ($data['quantite'] <= $produit->getMin()){
                    $panier->setQuantite($produit->getMin());
                }

                $panier->setCreatedAt(new \DateTimeImmutable());
                $panier->setProduit($produit);
                $panier->setUser($user);
                $entityManager->persist($panier);
                $entityManager->flush();

                $dataRes = $this->get('serializer')->serialize($panier, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else{
                $resultat = [
                    "message" => "updated"
                ];
                $dataRes = $this->get('serializer')->serialize($resultat, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

        } else {
            $res = [
                'message' => 'erreur'
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json');
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/show", name="panier_show", methods={"GET"})
     */
    public function show(Request $request, PanierRepository $panierRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $panierdata = $panierRepository->findOneBy(['id' => $data['panierId']]);
            if ($panierdata){
                $dataRes = $this->get('serializer')->serialize($panierdata, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else{
                $result = [
                    'message' => 'pas de données'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
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
     * @Route("/editQt", name="panier_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $panierdata = $panierRepository->findOneBy(['id' => $data['panierId']]);
            $produitdata = $produitRepository->findOneBy(['id'=>$panierdata->getProduit()->getId()]);

            if ($produitdata->getMax() > $data['quantite'] and $data['quantite']> $produitdata->getMin()){
                $panierdata->setQuantite($data['quantite']);
                $entityManager->persist($panierdata);
                $entityManager->flush();
                $dataRes = $this->get('serializer')->serialize($panierdata, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } elseif ($produitdata->getMax() <= $data['quantite']){
                $panierdata->setQuantite($produitdata->getMax());
                $entityManager->persist($panierdata);
                $entityManager->flush();
                $dataRes = $this->get('serializer')->serialize($panierdata, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } elseif ($data['quantite'] <= $produitdata->getMin()){
                $panierdata->setQuantite($produitdata->getMin());
                $entityManager->persist($panierdata);
                $entityManager->flush();
                $dataRes = $this->get('serializer')->serialize($panierdata, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else{
                $result = [
                    'message' => 'no update'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json', ['groups' => ['panier','user','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
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
     * @Route("/delete", name="panier_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $panierdata = $panierRepository->findOneBy(['id' => $data['panierId']]);
            $entityManager->remove($panierdata);
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
     * @Route("/showall", name="panier_showall", methods={"GET","POST"})
     */
    public function showall(Request $request, UserRepository $userRepository, PanierRepository $panierRepository): Response
    {
        if ($data = json_decode($request->getContent(), true)) {
            $userata = $userRepository->findOneBy(['id' => $data['userId']]);
            if ($userata) {
                $panierdata = $panierRepository->findBy(['user' => $userata]);
                $dataRes = $this->get('serializer')->serialize($panierdata, 'json', ['groups' => ['panier','user','produit', 'image']]);
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
