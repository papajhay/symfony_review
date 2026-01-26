<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LogTestController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    #[Route('/logger', name: 'app_log_test')]
    public function index(): Response
    {
        $this->logger->debug('Message DEBUG' );
        $this->logger->info('Message INFO');
        $this->logger->notice('Message NOTICE');
        $this->logger->warning('Message WARNING');
        $this->logger->error('Message ERROR');
        $this->logger->critical('Message CRITICAL');
        $this->logger->alert('Message ALERT');
        $this->logger->emergency('Message EMERGENCY');

        return $this->render('log_test/index.html.twig', [
            'controller_name' => 'LogTestController',
        ]);
    }
}
