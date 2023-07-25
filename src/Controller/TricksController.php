<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Form\TricksType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{
    #[Route('/tricks', name: 'create_tricks')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tricks = new Tricks();

        $form = $this->createForm(TricksType::class, $tricks);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tricks = $form->getData();

            $entityManager->persist($tricks);

            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('tricks/tricks.html.twig', [
            'form' => $form
        ]);
    }
}