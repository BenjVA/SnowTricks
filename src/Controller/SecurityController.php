<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'success' => 'Vous êtes bien connecté sur snowtricks !' ?? null
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgottenpassword', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request,
                                      UsersRepository $usersRepository,
                                      JWTService $JWTService,
                                      EntityManagerInterface $entityManager,
                                      SendMailService $mailService
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->findOneByMail($form->get('mail')->getData());

            if ($user) {
                // Generate token and store it
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];

                $payload = [
                    'user_id' => $user->getId()
                ];

                $token = $JWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));
                $user->setResetToken($token);

                $entityManager->persist($user);
                $entityManager->flush();

                // Generate link to reset password
                $url = $this->generateUrl('app_reset_password',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $context = [
                    'url' => $url,
                    'user' => $user
                ];

                $mailService->send(
                    'snowtricks@pro-blog.fr',
                    $user->getUserIdentifier(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    $context
                );
                $this->addFlash('success', 'Un email vous a été envoyé!');

                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'Un problème est survenu pendant la résiliation du mot de passe');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'resetPasswordRequestForm' => $form->createView()
        ]);
    }

    #[Route('/forgottenpassword/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token,
                                  Request $request,
                                  UsersRepository $usersRepository,
                                  EntityManagerInterface $entityManager,
                                  UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $usersRepository->findOneByResetToken($token);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');

                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'resetPasswordForm' => $form
            ]);
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }
}
