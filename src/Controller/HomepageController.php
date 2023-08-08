<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->findAll();

        return $this->render('homepage.html.twig', [
            'tricks' => $tricks
        ]);
    }
}