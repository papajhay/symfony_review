<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Genre;
use App\Entity\User;
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

        if (!in_array($attribute, [
            self::LIST,
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ])) {
            return false;
        }

        // LIST et CREATE peuvent recevoir class, null ou instance
        if (in_array($attribute, [self::LIST, self::CREATE])) {
            return $subject === null
                || $subject instanceof Genre
                || $subject === Genre::class;
        }

        // Les autres nécessitent une instance
        return $subject instanceof Genre;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // SUPER ADMIN passe toujours
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // ADMIN passe toujours
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return match ($attribute) {
            self::LIST, self::VIEW => $user->hasGroup('group_read'),
            self::CREATE => $user->hasGroup('group_create'),
            self::EDIT   => $user->hasGroup('group_edit'),
            self::DELETE => $user->hasGroup('group_delete'),
            default      => false,
        };
    }

}
