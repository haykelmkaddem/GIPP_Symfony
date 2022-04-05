<?php

namespace App\Controller;

use App\Entity\ProduitVendus;
use App\Form\ProduitVendusType;
use App\Repository\ProduitVendusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit/vendus")
 */
class ProduitVendusController extends AbstractController
{
    /**
     * @Route("/", name="app_produit_vendus_index", methods={"GET"})
     */
    public function index(ProduitVendusRepository $produitVendusRepository): Response
    {
        return $this->render('produit_vendus/index.html.twig', [
            'produit_venduses' => $produitVendusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_produit_vendus_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProduitVendusRepository $produitVendusRepository): Response
    {
        $produitVendu = new ProduitVendus();
        $form = $this->createForm(ProduitVendusType::class, $produitVendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitVendusRepository->add($produitVendu);
            return $this->redirectToRoute('app_produit_vendus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_vendus/new.html.twig', [
            'produit_vendu' => $produitVendu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_produit_vendus_show", methods={"GET"})
     */
    public function show(ProduitVendus $produitVendu): Response
    {
        return $this->render('produit_vendus/show.html.twig', [
            'produit_vendu' => $produitVendu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_produit_vendus_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProduitVendus $produitVendu, ProduitVendusRepository $produitVendusRepository): Response
    {
        $form = $this->createForm(ProduitVendusType::class, $produitVendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitVendusRepository->add($produitVendu);
            return $this->redirectToRoute('app_produit_vendus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_vendus/edit.html.twig', [
            'produit_vendu' => $produitVendu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_produit_vendus_delete", methods={"POST"})
     */
    public function delete(Request $request, ProduitVendus $produitVendu, ProduitVendusRepository $produitVendusRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitVendu->getId(), $request->request->get('_token'))) {
            $produitVendusRepository->remove($produitVendu);
        }

        return $this->redirectToRoute('app_produit_vendus_index', [], Response::HTTP_SEE_OTHER);
    }
}
