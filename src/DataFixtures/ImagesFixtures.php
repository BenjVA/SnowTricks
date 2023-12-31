<?php

namespace App\DataFixtures;

use App\Entity\Images;
use App\Repository\TricksRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private TricksRepository $tricksRepository, private ParameterBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $tricks = $this->tricksRepository->findAll();

        foreach ($tricks as $trick) {
            for ($img = 0; $img <= mt_rand(0, 3); $img++) {
                $file = md5(uniqid(rand(), true)) . '.jpeg';
                $image = new Images();
                $image->setName($file);
                $image->setTricks($trick);

                copy($this->parameterBag->get('image_fixtures_template'), $this->parameterBag->get('image_fixtures_directory') . $file);

                $images = new UploadedFile($this->parameterBag->get('image_fixtures_directory') . $file, 'Image', null, null, true);
                $images->move($this->parameterBag->get('images_directory') . '/tricks', $file);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TricksFixtures::class,
        ];
    }
}
