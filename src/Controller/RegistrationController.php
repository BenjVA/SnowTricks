<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request                     $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface  $userAuthenticator,
                             UsersAuthenticator          $authenticator,
                             EntityManagerInterface      $entityManager,
                             SendMailService             $mailService,
                             JWTService                  $JWTService
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

            // generate token
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $JWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mailService->send(
                'snowtricks@pro-blog.fr',
                $user->getUserIdentifier(),
                'Activation de votre compte sur le site snowtricks',
                'confirm_email',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );
            $this->addFlash('success', 'Un mail de confirmation vous a été envoyé!');

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

    #[Route('/verify/{token}', name: 'app_verify_email', methods: ['GET', 'POST'])]
    public function verifyUserEmail(string                 $token,
                                    JWTService             $JWTService,
                                    UsersRepository        $usersRepository,
                                    EntityManagerInterface $entityManager
    ): Response
    {
        if ($JWTService->isValid($token)
            && !$JWTService->isExpired($token)
            && $JWTService->checkTokenSignature($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $JWTService->getPayload($token);
            $user = $usersRepository->find($payload['user_id']);

            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'Utilisateur activé!');

                return $this->redirectToRoute('app_homepage');
            }
        }
        $this->addFlash('danger', 'Le token est expiré ou est invalide');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/resendverification', name: 'app_resend_verif', methods: ['GET', 'POST'])]
    public function resendVerificationMail(JWTService      $JWTService,
                                           SendMailService $mailService,
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        if ($user->getisVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');

            return $this->redirectToRoute('app_homepage');
        }

        // generate token
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $JWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mailService->send(
            'snowtricks@pro-blog.fr',
            $user->getUserIdentifier(),
            'Activation de votre compte sur le site snowtricks',
            'confirm_email',
            [
                'user' => $user,
                'token' => $token
            ]
        );
        $this->addFlash('success', 'Un mail de confirmation vous a été envoyé!');

        return $this->redirectToRoute('app_homepage');
    }
}
