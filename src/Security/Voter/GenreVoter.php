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

    public const LIST   = 'genre.list';
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
        if (!in_array($attribute, [
            self::LIST,
            self::VIEW,
            self::EDIT,
            self::DELETE,
            self::CREATE,
        ], true)) {
            return false;
        }

        // LIST & CREATE s'appliquent à la classe
        if (in_array($attribute, [self::LIST, self::CREATE], true)) {
            return $subject === null || $subject === Genre::class;
        }

        // VIEW, EDIT, DELETE nécessitent une instance
        return $subject instanceof Genre;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::LIST   => $this->canList($user),
            self::VIEW   => $this->canView(),
            self::CREATE => $this->canCreate(),
            self::EDIT   => $this->canEdit($subject),
            self::DELETE => $this->canDelete($subject),
            default      => false,
        };
    }

    private function canList(UserInterface $user): bool
    {

        return $this->security->isGranted('ROLE_USER');
    }

    private function canView(): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(Genre $genre): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canDelete(Genre $genre): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
