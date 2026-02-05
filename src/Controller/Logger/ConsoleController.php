<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

class ConsoleController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.console')]
        private LoggerInterface $logger
    ) {}
    #[Route('/console', name: 'app_console')]
    public function index(LoggerInterface $logger): Response
    {
        $this->logger->debug('Message DEBUG');
        $this->logger->info('Message INFO');
        $this->logger->notice('Message NOTICE');
        $this->logger->warning('Message WARNING');
        $this->logger->error('Message ERROR');
        $this->logger->critical('Message CRITICAL');
        $this->logger->alert('Message ALERT');
        $this->logger->emergency('Message EMERGENCY ');

        return new Response('Console logs OK');
    }
}
