<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use DateTimeImmutable;
use App\Repository\TricksRepository;
use App\Repository\UsersRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private TricksRepository $tricksRepository, private UsersRepository $usersRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $tricks = $this->tricksRepository->findAll();
        $users = $this->usersRepository->findAll();

        foreach ($tricks as $trick) {
            for ($cmnts = 0; $cmnts <= mt_rand(0,15); $cmnts++) {
                $comment = new Comments();

                $fakeCreatedAt = DateTimeImmutable::createFromMutable($faker->dateTimeThisMonth());
                $fakeUpdatedAt = new DateTimeImmutable();

                $comment->setContent($faker->sentences(2, true));
                $comment->setCreatedAt($fakeCreatedAt);
                $comment->setUpdatedAt($fakeUpdatedAt);
                $comment->setUsers($faker->randomElement($users));
                $comment->setTricks($trick);

                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TricksFixtures::class,
            UsersFixtures::class
        ];
    }
}
