<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($usr = 1; $usr <= 10; $usr++) {
            $user = new Users();
            // $password = $faker->password;
            $user->setMail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'Motdepasse'));
            $user->setIsVerified(true);

            $manager->persist($user);
        }

        $user = new Users();
        $user->setUsername('admin')
            ->setMail('benvalette539@gmail.com')
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($user, 'demo'));
        $manager->persist($user);
        $manager->flush();
    }
}
