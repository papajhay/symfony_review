<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RouterController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.router')]
        private LoggerInterface $logger
    ) {}

    #[Route('/router', name: 'router_logs')]
    public function index(): Response
    {
        $this->logger->info('Route analysée');

        return new Response('Router logs OK');
    }
}

