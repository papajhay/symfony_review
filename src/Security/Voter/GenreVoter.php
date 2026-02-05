<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Genre;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GenreVoter extends Voter
{
    private Security $security;

    public const VIEW   = 'genre.view';
    public const EDIT   = 'genre.edit';
    public const DELETE = 'genre.delete';
    public const CREATE = 'genre.create';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // VIEW & CREATE sans objet (liste + création)
        if (in_array($attribute, [self::VIEW, self::CREATE], true) && $subject === null) {
            return true;
    }

        // EDIT & DELETE nécessitent un Genre
        return $subject instanceof Genre
            && in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true);
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // VIEW & CREATE sans sujet
        if ($subject === null && in_array($attribute, [self::VIEW, self::CREATE], true)) {
            return true;
        }

        /** @var Genre $genre */
        $genre = $subject;

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::EDIT, self::DELETE =>
                $genre->getCreatedBy()?->getId() === $user->getId(),

            self::VIEW => true,

            default => false,
        };
    }
}
