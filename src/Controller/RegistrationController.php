<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UsersAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt,
    ): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            //Generate jwt token
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            $payload = [
                'user_id' => $user->getUserIdentifier()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'snowtricks@pro-blog.fr',
                $user->getUserIdentifier(),
                'Activation de votre compte sur Snowtricks',
                'confirmation_email',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );

            $this->addFlash('success', 'Un email de confirmation a été envoyé.');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verifyUserEmail($token,
                                    JWTService $jwt,
                                    UsersRepository $usersRepository,
                                    EntityManagerInterface $entityManager): Response
    {
        if ($jwt->isValid($token)
            && !$jwt->isExpired($token)
            && $jwt->checkTokenSignature($token, $this->getParameter('app.jwtsecret'))) {

            $payload = $jwt->getPayload($token);

            $user = $usersRepository->find($payload['user_id']);

            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'Vous avez bien activé votre compte !');

                return $this->redirectToRoute('app_homepage');
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/resendVerification', name: 'resend_verification')]
    public function resendVerification(JWTService $jwt, SendMailService $mail): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_homepage');
        }

        // Generate token
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'user_id' => $user->getUserIdentifier()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send(
            'snowtricks@pro-blog.fr',
            $user->getUserIdentifier(),
            'Activation de votre compte sur Snowtricks',
            'confirmation_email',
            [
                'user' => $user,
                'token' => $token
            ]
        );

        $this->addFlash('success', 'Email de vérification envoyé');

        return $this->redirectToRoute('app_homepage');
    }
}
