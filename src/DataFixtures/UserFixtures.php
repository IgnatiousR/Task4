<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher=$hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i=1;$i<=5;$i++){
            $user=new User();
            $email=$faker->email;
            $user->setEmail($email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->LastName);
            $user->setUuid(Uuid::v4());
            $user->setStatus("Active");
            $password=$this->hasher->hashPassword($user, $email);
            $user->setPassword($password);
            $user->setRegisterAt(new \DateTimeImmutable('now'));
            $user->setLoggedAt(new \DateTimeImmutable('now'));
            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
