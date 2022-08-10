<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\EntrepriseRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository, UserRepository $userRepository): Response
    {

            $verif = false;
            $allusers = $userRepository->findAll();
            foreach ($allusers as $verifuser){
                if ($verifuser->getEmail() == $request->get('email')){
                    $verif = true;
                }
            }
            if ($verif == false){
                $user = new User();
                $user->setNom($request->get('nom'));
                $user->setPrenom($request->get('prenom'));
                $user->setTelephone($request->get('telephone'));
                $user->setEmail($request->get('email'));
                $user->setIsVerified(false);
                $user->setIsBlocked(false);
                // encode the plain password
                $user->setPassword(
                    $userPasswordEncoder->encodePassword(
                        $user,
                        $request->get('password')
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('haykel.mkaddem1@esprit.tn', 'GIPP Support'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'id' => $user->getId(),
                        ])
                );
                // add and affect the company to the user
                $entreprise = new Entreprise();
                $entreprise->setNom($request->get('nomentreprise'));
                $entreprise->setAdresse($request->get('adresse'));
                $entreprise->setPays($request->get('pays'));
                $entreprise->setCodePostal($request->get('code_postal'));

                if($images = $request->files->get('document')){
                    $fichier = md5(uniqid()) . '.' . $images->guessExtension();
                    $images->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
                    $entreprise->setDocumentDeReference($fichier);
                }

                $entreprise->setLat($request->get('lat'));
                $entreprise->setLng($request->get('lng'));
                $entreprise->setUser($user);

                $entityManager->persist($entreprise);
                $entityManager->flush();

                $user->setEntreprise($entreprise);
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                $data = [
                    "message" => "ok"
                ];
                $dataRes = $this->get('serializer')->serialize($data, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                $data = [
                    "message" => "email existe!"
                ];
                $dataRes = $this->get('serializer')->serialize($data, 'json');
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {
            $userdata = $userRepository->find($data['userId']);
            $userdata->setIsVerified(true);

            $entityManager->persist($userdata);
            $entityManager->flush();

            $res = [
                'message' => "ok"
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
}