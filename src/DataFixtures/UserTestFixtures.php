<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserTestFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['username'=>'superadmin','email'=>'superadmin@test.local','password'=>'password','roles'=>['ROLE_SUPER_ADMIN']],
            ['username'=>'admin','email'=>'admin@test.local','password'=>'password','roles'=>['ROLE_ADMIN']],
            ['username'=>'user','email'=>'user@test.local','password'=>'password','roles'=>['ROLE_USER']],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
            $user->setIsVerified(true);

            $manager->persist($user);
            $this->addReference('test_user_' . $data['username'], $user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
