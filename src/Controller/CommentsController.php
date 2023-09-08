<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Form\CommentsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comments', name: 'app_comments_')]
class CommentsController extends AbstractController
{
    #[Route('/add/{id}', name: 'add', methods: ['GET', 'POST'])]
    public function add(Request $request, Tricks $tricks, EntityManagerInterface $entityManager): Response
    {
        $comments = new Comments();
        $form = $this->createForm(CommentsFormType::class, $comments, [
            'action' => $this->generateUrl('app_comments_add', ['id' => $tricks->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $now = new \DateTimeImmutable();

            $comments->setUsers($user)
                ->setTricks($tricks)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            $entityManager->persist($comments);
            $entityManager->flush();

            $slug = $tricks->getSlug();

            $this->addFlash('success', 'Commentaire ajouté avec succès!');

            return $this->redirectToRoute('app_tricks_details', [
                'slug' => $slug
            ]);
        }

        return $this->render('comments/add.html.twig', [
            'form' => $form->createView(),
            'comment' => $comments,
        ]);
    }

    /*#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Comments $comments, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $comments->getId(), $data['_token'])) {
            $entityManager->remove($comments);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé !');

            return new JsonResponse(['success' => true], 200);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }*/

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Comments $comments, Request $request, EntityManagerInterface $entityManager, Tricks $tricks): Response
    {
        $form = $this->createForm(CommentsFormType::class, $comments);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();

            $comments->setUpdatedAt($now);

            $entityManager->persist($comments);
            $entityManager->flush();

            $slug = $comments->getTricks()->getSlug();

            $this->addFlash('success', 'Commentaire modifié avec succès!');

            return $this->redirectToRoute('app_tricks_details', [
                'slug' => $slug
            ]);
        }

        return $this->render('comments/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comments,
        ]);
    }
}
