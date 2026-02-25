<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const LIST   = 'comment.list';
    public const VIEW   = 'comment.view';
    public const CREATE = 'comment.create';
    public const EDIT   = 'comment.edit';
    public const DELETE = 'comment.delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::LIST,
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ], true)) {
            return false;
        }

        if (in_array($attribute, [self::LIST, self::CREATE], true)) {
            return $subject === null
                || $subject instanceof Comment
                || $subject === Comment::class;
        }

        return $subject instanceof Comment;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // SUPER ADMIN bypass
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return match ($attribute) {

            self::LIST, self::VIEW => $user->hasGroup('group_read'),

            self::CREATE => $user->hasGroup('group_create'),

            self::EDIT =>
                $user->hasGroup('group_edit')
                || (
                    $subject instanceof Comment
                    && $subject->getCreatedBy()?->getId() === $user->getId()
                ),

            self::DELETE =>
                $user->hasGroup('group_delete')
                || (
                    $subject instanceof Comment
                    && $subject->getCreatedBy()?->getId() === $user->getId()
                ),

            default => false,
        };
    }
}
