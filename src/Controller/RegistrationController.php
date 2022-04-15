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
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository): Response
    {
        if($data = json_decode($request->getContent(), true)) {


            $user = new User();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setTelephone($data['telephone']);
            $user->setEmail($data['email']);
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $data['password']
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
            );
            // add and affect the company to the user
            $entreprise = new Entreprise();
            $entreprise->setNom($data['nomentreprise']);
            $entreprise->setAdresse($data['adresse']);
            $entreprise->setPays($data['pays']);
            $entreprise->setCodePostal($data['code_postal']);
            $entreprise->setDocumentDeReference($data['document']);
            $entreprise->setLat($data['lat']);
            $entreprise->setLng($data['lng']);
            $entreprise->setUser($user);

            $entityManager->persist($entreprise);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $data = [
                "user" => $user,
                "entreprise" => $entreprise
            ];
            $dataRes = $this->get('serializer')->serialize($data, 'json');
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
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}