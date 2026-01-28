<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DoctrineController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.doctrine')]
        private LoggerInterface $logger
    ) {}

    #[Route('/doctrine', name: 'doctrine_logs')]
    public function index(): Response
    {
        $this->logger->info('Requête Doctrine exécutée');

        return new Response('Doctrine logs OK');
    }
}
