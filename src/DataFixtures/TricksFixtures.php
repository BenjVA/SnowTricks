<?php

namespace App\DataFixtures;

use App\Entity\Tricks;
use App\Repository\UsersRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TricksFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger, private UsersRepository $usersRepository)
    {

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = $this->usersRepository->findAll();

        for ($trcks = 1; $trcks <= 10; $trcks++) {
            $tricks = new Tricks();
            $tricks->setName($faker->text(15));
            $tricks->setDescription($faker->text());
            $tricks->setSlug($this->slugger->slug(strtolower($tricks->getName())));
            $tricks->setGroupTricks($faker->text(10));
            $tricks->setUsers($faker->randomElement($users));
            $manager->persist($tricks);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
