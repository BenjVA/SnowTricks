<?php

namespace App\Controller;

use App\Entity\Tricks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    #[Route('/{slug}', name: 'details')]
    public function tricksDetails(Tricks $trick): Response
    {
        return $this->render('tricks/details.html.twig', [
            'trick' => $trick
        ]);
    }
}