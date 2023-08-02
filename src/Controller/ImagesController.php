<?php

namespace App\Controller;

use App\Entity\Images;
use App\Repository\ImagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ImagesController extends AbstractController
{
    public function index(Images $images): Response
    {
        $imageBase64 = base64_encode(stream_get_contents($images));

        return $this->render('homepage.html.twig');
    }
}