<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new Exception('Should never be reached');
    }
}