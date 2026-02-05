<?php declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use App\Security\Voter\CommentVoter;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commentaire')
            ->setEntityLabelInPlural('Commentaires')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un commentaire')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le commentaire')
//            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['content']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $can = function (string $permission) {
            return fn ($entity) =>
                $entity instanceof Comment
                && $this->isGranted($permission, $entity);
        };

        return $actions
            // PAGE INDEX
            ->update(Crud::PAGE_INDEX, Action::NEW, fn(Action $action) =>
            $action->setLabel('Ajouter un commentaire')
            )
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
            $action
                ->displayIf($can(CommentVoter::EDIT))
                ->setLabel('Modifier un commentaire')
            )
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn(Action $action) =>
            $action
                ->displayIf($can(CommentVoter::DELETE))
                ->setLabel('Supprimer un commentaire')
            )

            // PAGE EDIT
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, fn(Action $action) =>
            $action->setLabel('Enregistrer')
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, fn(Action $action) =>
            $action->setLabel('Enregistrer et continuer la modification')
            )

            // PAGE NEW
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, fn(Action $action) =>
            $action->setLabel('Créer')
            )
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE, fn(Action $action) =>
            $action->setLabel('Créer et continuer la modification')
            )
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, fn(Action $action) =>
            $action->setLabel('Créer et ajouter un autre commentaire')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('content', 'Commentaire'),
            AssociationField::new('book', 'Livre concerné')
                ->autocomplete(),
            AssociationField::new('createdBy', 'Auteur')
                ->onlyOnIndex()
                ->hideOnForm(),
            DateTimeField::new('createdAt', 'Posté le')
                ->onlyOnIndex()
                ->hideWhenCreating(),
        ];
    }

    public function createEntity(string $entityFqcn): Comment
    {
        $comment = new Comment();
        $comment->setCreatedBy($this->getUser());

        return $comment;
    }
}
