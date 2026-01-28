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
        private LoggerInterface $logger,
        private HttpClientInterface $httpClient
    ) {}

    #[Route('/http-client', name: 'http_client_logs')]
    public function index(): Response
    {
        $this->logger->info('Appel HTTP externe démarré');

        try {
            $response = $this->httpClient->request('GET', 'https://httpbin.org/status/500');

            $this->logger->info('Statut HTTP', [
                'status' => $response->getStatusCode(),
            ]);

            // Déclencher une exception si code HTTP >= 400
            if ($response->getStatusCode() >= 400) {
                throw new HttpException(
                    $response->getStatusCode(),
                    'Erreur HTTP détectée lors de l’appel à HTTPBin'
                );
            }

        } catch (HttpException $e) {
            $this->logger->error('HttpException capturée', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);

            // Optionnel : ré-lancer pour que Symfony gère la réponse HTTP
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Autre exception', ['exception' => $e]);
            throw $e;
        }

        return new Response('HTTP Client OK');
    }
}
