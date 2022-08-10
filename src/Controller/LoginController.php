<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class LoginController extends AbstractController
{
    /**
     * @Route("/loginUser", name="loginUser")
     */
    public function loginUser(Request $request, EncoderFactoryInterface $encoderFactory, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        if($data = json_decode($request->getContent(), true)) {

            $userdata = $userRepository->findOneBy(['email' => $data['email']]);
            if ($userdata && $userdata->getIsBlocked() == false) {
                $encoder = $encoderFactory->getEncoder($userdata);
                if ($encoder->isPasswordValid($userdata->getPassword(), $data['password'], null) && $userdata->isVerified()== true) {
                    $res = [
                        "user" => $userdata,
                        "message" => "ok"
                    ];
                    $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
                    $response = new Response($dataRes);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } elseif ($encoder->isPasswordValid($userdata->getPassword(), $data['password'], null) && $userdata->isVerified()== false){
                    $res = [
                        'message' => 'Verify Your Account!!'
                    ];
                    $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
                    $response = new Response($dataRes);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } else {
                    $res = [
                        'message' => 'password wrong'
                    ];
                    $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
                    $response = new Response($dataRes);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

            } elseif ($userdata && $userdata->getIsBlocked() == true) {
                $res = [
                    'message' => 'Your Account is Blocked!!'
                ];
                $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                $res = [
                    'message' => "email not found"
                ];
                $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
                $response = new Response($dataRes);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        } else {
            $res = [
                'message' => "no data"
            ];
            $dataRes = $this->get('serializer')->serialize($res, 'json', ['groups' => ['user', 'entreprise', 'userEntreprise']]);
            $response = new Response($dataRes);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
