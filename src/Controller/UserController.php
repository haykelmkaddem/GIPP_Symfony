<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\EntrepriseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = new User();
            $passwordCrypted = $userPasswordEncoder->encodePassword(
                $user,
                $data['password']
            );
            $res = $userRepository->findOneBy(['email' => $data['email'], 'password' => $passwordCrypted]);
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
     * @Route("/show", name="user_show", methods={"GET", "POST"})
     */
    public function show(Request $request, UserRepository $userRepository, EntrepriseRepository $entrepriseRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $userEntreprise = $userRepository->findOneBy(['id' => $data['userId']]);
            $entreprise = $entrepriseRepository->findOneBy(['user' => $userEntreprise]);

            $resultat = [
                $userEntreprise,$entreprise
            ];
            $dataRes = $this->get('serializer')->serialize($userEntreprise, 'json', ['groups' => ['user','entreprise','userEntreprise']]);
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
     * @Route("/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, EntrepriseRepository $entrepriseRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);
            $entreprise = $entrepriseRepository->findOneBy(['user'=>$user]);

            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setTelephone($data['telephone']);

            $entityManager->persist($user);
            $entityManager->flush();

            $entreprise->setNom($data['nomentreprise']);
            $entreprise->setAdresse($data['adresse']);
            $entreprise->setPays($data['pays']);
            $entreprise->setCodePostal($data['code_postal']);

            $entityManager->persist($user);
            $entityManager->flush();

            $resultat = [
                $user,$entreprise
            ];
            $dataRes = $this->get('serializer')->serialize($user, 'json', ['groups' => ['user','entreprise','userEntreprise']]);
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
     * @Route("/verifierPassword", name="user_verifierPassword", methods={"GET", "POST"})
     */
    public function verifierPassword(Request $request, EntityManagerInterface $entityManager, EncoderFactoryInterface $encoderFactory, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);

            $encoder = $encoderFactory->getEncoder($user);

            if($encoder->isPasswordValid($user->getPassword(),$data['password'],null)){

                $result = [
                    'verif' => true
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else{
                $result = [
                    'verif' => false
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
     * @Route("/editPassword", name="user_editPassword", methods={"GET", "POST"})
     */
    public function editPassword(Request $request, EntityManagerInterface $entityManager, EncoderFactoryInterface $encoderFactory, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);
            $pass = $data['password'];
            $passwordCrypted = $userPasswordEncoder->encodePassword(
                $user,
                $data['password']
            );
            $newPass = $data['newpassword'];
            $encoder = $encoderFactory->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(),$data['password'],null)){
                $user->setPassword(
                    $userPasswordEncoder->encodePassword(
                        $user,
                        $newPass
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $result = [
                    'message' => 'updated'
                ];
                $dataRes = $this->get('serializer')->serialize($result, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else{
                $result = [
                    'message' => 'Vérifier Votre Mot De Passe'
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
     * @Route("/delete", name="user_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = $userRepository->findOneBy(['id'=>$data['userId']]);

            $entityManager->remove($user);
            $entityManager->flush();

            $result = [
                'message' => 'success'
            ];

            $dataRes = $this->get('serializer')->serialize($result, 'json');
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
     * @Route("/tog", name="tog")
     */
    public function tog(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $user = new User();
            $passwordCrypted = $userPasswordEncoder->encodePassword(
                $user,
                $data['password']
            );
            $res = $userRepository->findOneBy(['email' => $data['email'], 'password' => $passwordCrypted]);
            $dataRes = $this->get('serializer')->serialize($res, 'json');
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
