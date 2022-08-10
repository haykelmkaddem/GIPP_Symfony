<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/categorie")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="categorie_index", methods={"GET"})
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $categorie = new Categorie();
            $categorie->setNom($data['nom']);
            $entityManager->persist($categorie);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($categorie, 'json', ['groups' => ['categorie','produit']]);
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
     * @Route("/show", name="categorie_show", methods={"GET", "POST"})
     */
    public function show(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $categorydata = $categorieRepository->findOneBy(['id' => $data['categorieId']]);
            $dataRes = $this->get('serializer')->serialize($categorydata, 'json', ['groups' => ['categorie','produit','image']]);
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
     * @Route("/edit", name="categorie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request,  EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {

            // $categorydata = $entityManager->getRepository(Categorie::class)->find($data['categorieId']);
            $categorydata = $categorieRepository->findOneBy(['id' => $data['categorieId']]);
            if ($categorydata){
                $categorydata->setNom($data['nom']);
                $entityManager->persist($categorydata);
                $entityManager->flush();
                $dataRes = $this->get('serializer')->serialize($categorydata, 'json', ['groups' => ['categorie','produit']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else {
                $result = [
                    'message' => 'pas de données'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
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
     * @Route("/delete", name="categorie_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $categorydata = $categorieRepository->find($data['categorieId']);
            if ($categorydata){
                $entityManager->remove($categorydata);
                $entityManager->flush();
                $result = [
                    'message' => 'removed'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else {
                $result = [
                    'message' => 'pas de données'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

        }else {
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
     * @Route("/showall", name="categorie_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        $categorydata = $categorieRepository->findAll();
        if($categorydata) {
            $dataRes = $this->get('serializer')->serialize($categorydata, 'json', ['groups' => ['categorie','produit']]);
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
