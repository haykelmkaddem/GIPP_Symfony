<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ImageRepository;
use App\Repository\ProduitRepository;
use App\Repository\ProduitVendusRepository;
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
            $produit = new Produit();
            $produit->setNom($request->get('nom'));
            $produit->setNomAr($request->get('nomAr'));
            $produit->setNomEn($request->get('nomEn'));
            $produit->setNomIt($request->get('nomIt'));
            $produit->setDescription($request->get('description'));
            $produit->setDescriptionAr($request->get('descriptionAr'));
            $produit->setDescriptionEn($request->get('descriptionEn'));
            $produit->setDescriptionIt($request->get('descriptionIt'));
            $produit->setVisibilite($request->get('visibilite'));
            $produit->setStock($request->get('stock'));
            $produit->setPrix($request->get('prix'));
            $produit->setMin($request->get('min'));
            $produit->setMax($request->get('max'));
            $produit->setVu(0);
            if ($request->get('discount') == "a"){
                $produit->setDiscount(null);
            } else{
                $produit->setDiscount($request->get('discount'));
            }

            $categorie = $categorieRepository->find($request->get('categorieId'));
            $produit->setCategorie($categorie);

            $entityManager->persist($produit);
            $entityManager->flush();

        if($images = $request->files->get('assets')){
            foreach ($images as $image) {

                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Image();
                $img->setImageURL($fichier);
                $img->setProduit($produit);
                $entityManager->persist($img);
                $entityManager->flush();

            }

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
            $dataRes = $this->get('serializer')->serialize($produitdata, 'json', ['groups' => ['produit','pourproduit','avis', 'produitvendus','forproduct', 'image']]);
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
            $dataRes = $this->get('serializer')->serialize($produitdata, 'json', ['groups' => ['produit','pourproduit', 'produitvendus','forproduct', 'image', 'forproductavis', 'avisforproduct']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

    }


    /**
     * @Route("/newImage", name="produit_newImage", methods={"GET", "POST"})
     */
    public function newImage(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, ImageRepository $imageRepository): Response
    {

        //image upload
        if($images = $request->files->get('assets')){
$pp="123";

            foreach ($images as $image) {

                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $pp = $fichier;
                $img = new Image();
                $img->setImageURL($fichier);
                $img->setProduit($produitRepository->findOneBy(['id' => 1]));
                $entityManager->persist($img);
                $entityManager->flush();

            }
            $res = [
                'message'=> "data"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['produit','pourproduit','avis', 'produitvendus','forproduct']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else{
            $res = [
                'message'=> "no data"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['produit','pourproduit','avis', 'produitvendus','forproduct']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/produitstat", name="produit_produitstat", methods={"GET", "POST"})
     */
    public function produitstat(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, ProduitVendusRepository $produitVendusRepository): Response
    {
        $produitVendus = $produitVendusRepository->findAll();
        $produits = $produitRepository->findAll();
        $final = [];
        $k = 0;
        foreach ($produits as $produit){
            $k = $k +1;
            if ($k < 6){
            $i = 0;
            foreach ($produitVendus as $pvendus){
                if ($pvendus->getProduit() == $produit){
                    $i = $i + $pvendus->getQuantite();
                }
            }
            $p = ['produit'=> $produit,
                'qt'=> $i];
            array_push($final, $p);
            }
        }
        $dataRes = $this->get('serializer')->serialize($final, 'json', ['groups' => ['produit','pourproduit','avis', 'produitvendus','forproduct', 'image']]);
        $response = new Response($dataRes);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @Route("/addVu", name="produit_addVu", methods={"GET", "POST"})
     */
    public function addVu(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produitdata = $produitRepository->findOneBy(['id' => $data['produitId']]);

            $produitdata->setVu($produitdata->getVu() + 1);
            $entityManager->persist($produitdata);
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
     * @Route("/updateVisibilite", name="produit_updateVisibilite", methods={"GET", "POST"})
     */
    public function updateVisibilite(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $produitdata = $produitRepository->findOneBy(['id' => $data['produitId']]);
            $produitdata->setVisibilite(!$produitdata->getVisibilite());

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
}
