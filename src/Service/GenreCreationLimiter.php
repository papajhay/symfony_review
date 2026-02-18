<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GenreCreationLimiter
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $genreCreationLimiter
    ) {}

    public function consume(UserInterface $user): array
    {
        $identifier = $user->getUserIdentifier();

        $limiter = $this->genreCreationLimiter->create($identifier);
        $result = $limiter->consume(1);

        if (!$result->isAccepted()) {
            $retryAfter = $result->getRetryAfter()->getTimestamp() - time();

            return [
                'allowed' => false,
                'retryAfter' => $retryAfter
            ];
        }

        return [
            'allowed' => true,
            'retryAfter' => 0
        ];
    }
}

