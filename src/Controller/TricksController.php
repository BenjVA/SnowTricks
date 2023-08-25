<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\TricksFormType;
use App\Repository\CommentsRepository;
use App\Service\ImageService;
use App\Service\UrlToEmbedUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    #[Route('/details/{slug}', name: 'details')]
    public function tricksDetails(Tricks $trick, CommentsRepository $commentsRepository): Response
    {
        $comments = $commentsRepository->findAll();

        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'comments' => $comments
        ]);
    }

    #[Route('/add', name: 'add')]
    public function addTricks(Request                $request,
                              EntityManagerInterface $entityManager,
                              SluggerInterface       $slugger,
                              ImageService           $imageService,
                              UrlToEmbedUrl          $urlToEmbedUrl
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $tricks = new Tricks();
        $form = $this->createForm(TricksFormType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get image and videos
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichier = $imageService->add($image);

                $img = new Images();
                $img->setName($fichier);
                $tricks->addImage($img);
                $tricks->removeImage($image);
            }
            $videos = $form->get('videos')->getData();

            foreach ($videos as $video) {
                $embedUrl = $urlToEmbedUrl->toEmbedUrl($video);
                $vid = new Videos();
                $vid->setUrl($embedUrl);
                $tricks->addVideos($vid);
            }


            $slug = $slugger->slug(strtolower($tricks->getName()));
            $tricks->setSlug($slug);

            $user = $this->getUser();
            $tricks->setUsers($user);

            $entityManager->persist($tricks);
            $entityManager->flush();

            $this->addFlash('success', 'Figure ajoutée avec succès!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('tricks/add.html.twig', [
            'form' => $form->createView(),
            'trick' => $tricks
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit')]
    public function edit(Tricks                 $tricks,
                         Request                $request,
                         ImageService           $imageService,
                         EntityManagerInterface $entityManager,
                         SluggerInterface       $slugger,
                         UrlToEmbedUrl          $urlToEmbedUrl
    ): Response
    {
        $form = $this->createForm(TricksFormType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get image and videos
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichier = $imageService->add($image);

                $img = new Images();
                $img->setName($fichier);
                $tricks->addImage($img);
            }

            $videos = $form->get('videos')->getData();

            foreach ($videos as $video) {
                $embedUrl = $urlToEmbedUrl->toEmbedUrl($video->getUrl());
                $vid = new Videos();
                $vid->setUrl($embedUrl);
                $tricks->addVideos($vid);
                $tricks->removeVideo($video);
            }

            $slug = $slugger->slug(strtolower($tricks->getName()));
            $tricks->setSlug($slug);

            $user = $this->getUser();
            $tricks->setUsers($user);

            $entityManager->persist($tricks);
            $entityManager->flush();

            $this->addFlash('success', 'Figure modifiée avec succès!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('tricks/edit.html.twig', [
            'form' => $form->createView(),
            'tricks' => $tricks
        ]);
    }

    #[Route('/delete/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $images, Request $request, EntityManagerInterface $entityManager, ImageService $imageService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $images->getId(), $data['_token'])) {
            $imageName = $images->getName();

            $imageService->delete($imageName, '');
            $entityManager->remove($images);
            $entityManager->flush();

            $this->addFlash('success', 'Image supprimée !');

            return new JsonResponse(['success' => true], 200);
            }
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}