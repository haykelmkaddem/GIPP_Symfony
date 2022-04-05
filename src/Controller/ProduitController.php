<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['description']);
            $produit->setStock($data['stock']);
            $produit->setPrix($data['prix']);
            $produit->setMin($data['min']);
            $produit->setMax($data['max']);
            $categorie = $categorieRepository->find($data['categorieId']);
            $produit->setCategorie($categorie);
            $entityManager->persist($produit);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($produit, 'json', ['groups' => ['produit','pourproduit','panier', 'avis', 'produitvendus','forproduct']]);
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
     * @Route("/show", name="produit_show", methods={"GET", "POST"})
     */
    public function show(Request $request, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produitdata = $produitRepository->findOneBy(['id' => $data['produitId']]);
            $dataRes = $this->get('serializer')->serialize($produitdata, 'json', ['groups' => ['produit','pourproduit','panier', 'avis', 'produitvendus','forproduct']]);
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
     * @Route("/edit", name="produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produitdata = $produitRepository->findOneBy(['id' => $data['produitId']]);
            $produitdata->setNom($data['nom']);
            $produitdata->setDescription($data['description']);
            $produitdata->setStock($data['stock']);
            $produitdata->setPrix($data['prix']);
            $produitdata->setMin($data['min']);
            $produitdata->setMax($data['max']);
            $categorieData = $categorieRepository->findOneBy(['id' => $data['categorieId']]);
            $produitdata->setCategorie($categorieData);

            $entityManager->persist($produitdata);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($produitdata, 'json', ['groups' => ['produit','pourproduit','panier', 'avis', 'produitvendus','forproduct']]);
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
     * @Route("/delete", name="produit_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produitdata = $produitRepository->findOneBy(['id' => $data['produitId']]);

            $entityManager->remove($produitdata);
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
     * @Route("/showall", name="produit_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produitdata = $produitRepository->findAll();
        if($produitdata) {
            $dataRes = $this->get('serializer')->serialize($produitdata, 'json', ['groups' => ['produit','pourproduit','panier', 'avis', 'produitvendus','forproduct']]);
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
