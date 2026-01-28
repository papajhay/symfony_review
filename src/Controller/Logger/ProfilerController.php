<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilerController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.profiler')]
        private LoggerInterface $logger
    ) {}

    #[Route('/profiler', name: 'profiler_logs')]
    public function index(): Response
    {
        $this->logger->info('Profilage actif');

        return new Response('Profiler logs OK');
    }
}

