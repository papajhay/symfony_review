<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Security\Voter\GenreVoter;
use App\Service\GenreCreationLimiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/genre')]
class GenreController extends AbstractController
{
    #[Route('', name: 'genre_index', methods: ['GET'])]
    public function index(GenreRepository $repository, Request $request): Response
    {
        $this->denyAccessUnlessGranted(GenreVoter::LIST, Genre::class);

        $limit = 5;
        $page = max(1, $request->query->getInt('page', 1));
        $offset = ($page - 1) * $limit;
        $offset = ($page - 1) * $limit;

        // Récupération des genres avec pagination
        $genres = $repository->createQueryBuilder('g')
            ->orderBy('g.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Nombre total de genres pour calculer les pages
        $totalGenres = $repository->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $totalPages = ceil($totalGenres / $limit);

        return $this->render('genre/list.html.twig', [
            'genres' => $genres,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'genre_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        GenreCreationLimiter $creationLimiter
    ): Response
    {
        $genre = new Genre();

        if (!$this->isGranted(GenreVoter::CREATE, $genre)) {
            $this->addFlash('error', 'Vous n’avez pas la permission de créer un genre.');
            return $this->redirectToRoute('genre_index');
        }

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $limit = $creationLimiter->consume($this->getUser());

            if (!$limit['allowed']) {
                $this->addFlash(
                    'error',
                    "Vous avez atteint la limite de création. Réessayez dans {$limit['retryAfter']} secondes."
                );

                return $this->redirectToRoute('genre_index');
            }

            $genre->setCreatedBy($this->getUser());

            $em->persist($genre);
            $em->flush();

            $this->addFlash('success', 'Genre créé avec succès.');

            return $this->redirectToRoute('genre_index');
        }

        return $this->render('genre/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'genre_show', methods: ['GET'])]
    #[IsGranted(GenreVoter::VIEW, subject: 'genre')]
    public function show(Genre $genre): Response
    {
        return $this->render('genre/show.html.twig', [
            'genre' => $genre,
        ]);
    }

    #[Route('/{id}/edit', name: 'genre_edit', methods: ['GET', 'POST'])]
    #[IsGranted(GenreVoter::EDIT, subject: 'genre')]
    public function edit(Request $request, Genre $genre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Genre modifié.');

            return $this->redirectToRoute('genre_index');
        }

        return $this->render('genre/edit.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'genre_delete', methods: ['POST'])]
    #[IsGranted(GenreVoter::DELETE, subject: 'genre')]
    public function delete(Request $request, Genre $genre, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $genre->getId(), $request->request->get('_token'))) {
            $em->remove($genre);
            $em->flush();

            $this->addFlash('success', 'Genre supprimé.');
        }

        return $this->redirectToRoute('genre_index');
    }
}
