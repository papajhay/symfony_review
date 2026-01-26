<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{

    #[Route('/book', name: 'app_book')]
    public function index(EntityManagerInterface $em): Response
    {
        $book1 = new Book();
        $book1->setTitle('Vakivakim-piainana');
        $book1->setAuthor('Iharilanto Patrick Andriamangatiana');
        $book1->setPublishedAt(new \DateTimeImmutable('1995-01-01'));
        $em->persist($book1);

        $book2 = new Book();
        $book2->setTitle('Mitaraina ny tany');
        $book2->setAuthor(' Andry Andraina');
        $book2->setPublishedAt(new \DateTimeImmutable('1975-01-01'));
        $em->persist($book2);

        $em->flush();

        $allBooks = $em->getRepository(Book::class)->findAll();


        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
}
