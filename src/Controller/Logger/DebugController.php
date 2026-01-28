<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DebugController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.debug_channel')]
        private LoggerInterface $logger
    ) {}

    #[Route('/debug', name: 'debug_logs')]
    public function index(): Response
    {
        $this->logger->debug('Debug technique');

        return new Response('Debug logs OK');
    }
}

