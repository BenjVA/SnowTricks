<?php

namespace App\DataFixtures;

use App\Entity\Videos;
use App\Repository\TricksRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VideosFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private TricksRepository $tricksRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $videosLinks = [
            'https://www.youtube.com/embed/jH76540wSqU',
            'https://www.youtube.com/embed/6yA3XqjTh_w',
            'https://www.dailymotion.com/embed/video/xq963',
            'https://www.youtube.com/embed/QF2rtZBsjIo',
            'https://www.dailymotion.com/embed/video/x4tegp',
            'https://www.dailymotion.com/embed/video/xgdlde',
            'https://www.youtube.com/embed/oAK9mK7wWvw',
            'https://www.youtube.com/embed/FMHiSF0rHF8',
            'https://www.dailymotion.com/embed/video/x23jln',
            'https://www.dailymotion.com/embed/video/xh94s2'
        ];

        $tricks = $this->tricksRepository->findAll();

        foreach ($tricks as $trick) {
            for ($trcks = 0; $trcks <= mt_rand(0, 3); $trcks++) {
                $videosUrl = $faker->randomElement($videosLinks);

                $video = new Videos();
                $video->setTricks($trick);
                $video->setUrl($videosUrl);

                $manager->persist($video);
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
