<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Form\CommentsFormType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comments', name: 'app_comments_')]
class CommentsController extends AbstractController
{
    #[Route('/add/{id}', name: 'add')]
    public function add(Request $request, Tricks $tricks, EntityManagerInterface $entityManager): Response
    {
        $comments = new Comments();
        $form = $this->createForm(CommentsFormType::class, $comments, [
            'action' => $this->generateUrl('app_comments_add', ['id' => $tricks->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $now = New \DateTimeImmutable();

            $comments->setUsers($user)
                ->setTricks($tricks)
                ->setCreatedAt($now);

            $entityManager->persist($comments);
            $entityManager->flush();

            $slug = $tricks->getSlug();

            $this->addFlash('success', 'Commentaire ajouté avec succès!');

            return $this->redirectToRoute('app_tricks_details' , [
                'slug' => $slug
            ]);
        }

        return $this->render('comments/add.html.twig' , [
            'form' => $form->createView(),
            'comment' => $comments,
        ]);
    }
}
