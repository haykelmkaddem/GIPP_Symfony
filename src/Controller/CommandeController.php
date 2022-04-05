<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\ProduitVendus;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\ProduitVendusRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, UserRepository $userRepository, PanierRepository $panierRepository, ProduitRepository $produitRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $commande = new Commande();
            $commande->setReference(bin2hex(random_bytes(10)));
            $commande->setMethodeDePaiement($data['methodeDePaiement']);
            $commande->setCommentaire($data['commentaire']);
            $commande->setTotale($data['totale']);
            $commande->setStatutCommande($data['statutCommande']);
            $commande->setCreatedAt(date_create_immutable('now'));
            $commande->setDateModification(date_create_immutable('now'));
            $userdata = $userRepository->find($data['userId']);
            $commande->setUser($userdata);
            $entityManager->persist($commande);
            $entityManager->flush();

            //email de confirmation
            $email = (new Email())
                ->from('haykel.mkaddem1@esprit.tn')
                ->to('mkaddemhaykel@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('[GIPP] Confirmation De Commande10')
                //->text('Sending emails is fun again!')
                ->html('<div style="align-self: center">
                            <img src={{asset("http://127.0.0.1:8000/image/logo.png")}} width="50px" height="50px">
                            <h1>BONJOUR HAYKEL MKADDEM,</h1>
                            <h2>MERCI DAVOIR EFFECTUÉ VOS ACHATS SUR GIPP!</h2>
                            <h3>REFERENCE DE COMMANDE EST</h3>
                         </div>');
            $mailer->send($email);

            $listproduitpanier = $panierRepository->findBy(['user' => $userdata]);
            foreach ($listproduitpanier as $panier){
                $produitVendus = new ProduitVendus();
                $produitVendus->setCommande($commande);
                $produitVendus->setProduit($panier->getProduit());
                $produit = $panier->getProduit();
                $produitVendus->setNom($produit->getNom());
                $produitVendus->setQuantite($panier->getQuantite());
                $produitVendus->setPrix($produit->getPrix());
                $produitVendus->setTotale($panier->getQuantite() * $produit->getPrix());
                $entityManager->persist($produitVendus);
                $entityManager->flush();

                $produitToUpdate = $produitRepository->findOneBy(['id'=>$produit->getId()]);
                $produitToUpdate->setStock($produitToUpdate->getStock() - $panier->getQuantite());
                if ($produitToUpdate->getMax()> $produitToUpdate->getStock()){
                    $produitToUpdate->setMax($produitToUpdate->getStock());
                }

                $entityManager->remove($panier);
                $entityManager->flush();
            }

            $dataRes = $this->get('serializer')->serialize($commande, 'json', ['groups' => ['commande','user']]);
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
     * @Route("/show", name="commande_show", methods={"GET", "POST"})
     */
    public function show(Request $request, CommandeRepository $commandeRepository, ProduitVendusRepository $produitVendusRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $commande = $commandeRepository->findOneBy(['id'=>$data['commandeId']]);
            $listproduitVendus = $produitVendusRepository->findBy(['commande'=>$commande]);
            $resultat = [
                $commande,$listproduitVendus
            ];
            $dataRes = $this->get('serializer')->serialize($resultat, 'json', ['groups' => ['commande','user','produitvendus']]);
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
     * @Route("/edit", name="commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $commandeData = $commandeRepository->findOneBy(['id'=>$data['commandeId']]);
            $commandeData->setStatutCommande($data['statutCommande']);
            $commandeData->setDateModification(date_create_immutable('now'));
            $entityManager->persist($commandeData);
            $entityManager->flush();

            $dataRes = $this->get('serializer')->serialize($commandeData, 'json', ['groups' => ['commande','user','produitvendus']]);
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
     * @Route("/delete", name="commande_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, ProduitVendusRepository $produitVendusRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $commandeData = $commandeRepository->findOneBy(['id'=>$data['commandeId']]);
            $listeProduitVendusCommande = $produitVendusRepository->findBy(['commande'=>$commandeData]);
            foreach ($listeProduitVendusCommande as $produitVendus){
                $entityManager->remove($produitVendus);
                $entityManager->flush();
            }
            $entityManager->remove($commandeData);
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
     * @Route("/showall", name="commande_showall", methods={"GET", "POST"})
     */
    public function showall(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commandeData = $commandeRepository->findAll();

        if($commandeData) {
            $dataRes = $this->get('serializer')->serialize($commandeData, 'json', ['groups' => ['commande','user','produitvendus']]);
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

    /**
     * @Route("/showusercommande", name="commande_showuser", methods={"GET", "POST"})
     */
    public function showusercommande(Request $request, CommandeRepository $commandeRepository, UserRepository $userRepository): Response
    {
        if ($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);
            $listcommandeUser = $commandeRepository->findBy(['user'=>$user]);

            $dataRes = $this->get('serializer')->serialize($listcommandeUser, 'json', ['groups' => ['commande','user','produitvendus']]);
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
