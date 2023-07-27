<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    #[Route('/users', name: 'login_user')]
    public function loginUser(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new Users();
        $user->setUsername('User1');
        $user->setMail('user1@exemple.com');
        $user->setPassword('Motdepasse');

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('Created new users with id '.$user->getId());
    }
}