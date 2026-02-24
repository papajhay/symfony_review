<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // données réalistes en français

        $usersData = [
            ['prefix' => 'superadmin', 'count' => 1, 'roles' => ['ROLE_SUPER_ADMIN'], 'groups' => []],
            ['prefix' => 'admin',      'count' => 2, 'roles' => ['ROLE_ADMIN'],       'groups' => ['genre_list','genre_create','genre_edit','genre_delete']],
            ['prefix' => 'user',       'count' => 4, 'roles' => ['ROLE_USER'],        'groups' => ['genre_list','genre_view']],
        ];

        foreach ($usersData as $tpl) {
            for ($i = 1; $i <= $tpl['count']; $i++) {
                $username = $tpl['prefix'] . ($tpl['count'] > 1 ? $i : '');

                $user = new User();
                $user->setUsername($username);

                // Email aléatoire mais unique
                $user->setEmail($faker->unique()->safeEmail());

                $user->setRoles($tpl['roles']);
                $user->setPassword(
                    $this->passwordHasher->hashPassword($user, 'password')
                );

                $groups = $tpl['groups'];

                // Exemple : on ne donne jamais de création/modification/suppression aux ROLE_USER
                if ($tpl['prefix'] === 'user') {
                    // lecture seule : genre_list + genre_view
                    $groups = ['genre_list', 'genre_view'];
                }

                $user->setGroups($groups);
                $user->setIsVerified(true);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
