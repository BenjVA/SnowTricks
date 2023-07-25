<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/users', name: 'create_user')]
    public function createUser(EntityManagerInterface $entityManager): Response
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