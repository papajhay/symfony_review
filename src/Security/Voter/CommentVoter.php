<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Comment
            && in_array($attribute, [self::EDIT, self::DELETE], true);
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

        /** @var Comment $comment */
        $comment = $subject;

        // SUPER ADMIN : accès total
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // ️ Auteur du commentaire
        return $comment->getCreatedBy() !== null
            && $comment->getCreatedBy()->getId() === $user->getId();
    }
}
