<?php declare(strict_types=1);

namespace App\Controller\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpClientController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.http_client')]
        private LoggerInterface $logger
    ) {}

    #[Route('/http-client', name: 'http_client_logs')]
    public function index(): Response
    {
        $this->logger->info('Appel HTTP démarré');

        $statusCode = 500;

        switch ($statusCode) {
            case 200:
                $this->logger->info('API OK');
                break;

            case 401:
                $this->logger->warning('Non autorisé');
                break;

            default:
                $this->logger->error('Erreur HTTP');
        }

        return new Response('Fin OK');
    }
}
