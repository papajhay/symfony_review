<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $book = new Book();

            // Titre aléatoire
            $book->setTitle(implode(' ', $faker->words(3))); // phrase de 3 mots

            // Auteur aléatoire
            $book->setAuthor($faker->name());

            // Date de publication aléatoire
            $date = $faker->dateTimeBetween('-30 years', 'now');

            $book->setPublishedAt(
                \DateTimeImmutable::createFromMutable($date)
            );

            $manager->persist($book);
        }

        $manager->flush();
    }
}
