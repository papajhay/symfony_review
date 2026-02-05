<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Livre')
            ->setEntityLabelInPlural('Livres')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des livres')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un livre')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le livre');
//            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // PAGE INDEX
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) =>
            $action->setLabel('Ajouter un livre')
            )
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')

            // PAGE NEW
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, fn (Action $action) =>
            $action->setLabel('Créer')
            )
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, fn (Action $action) =>
            $action->setLabel('Créer et ajouter un autre livre')
            )

            // PAGE EDIT
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
            $action
                ->setLabel('Modifier un livre')
            )
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn(Action $action) =>
            $action
                ->setLabel('Supprimmer un livre')
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, fn (Action $action) =>
            $action->setLabel('Enregistrer')
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, fn (Action $action) =>
            $action->setLabel('Enregistrer et continuer la modification')
            );
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextField::new('author', 'Auteur'),

            DateField::new('publishedAt', 'Date de publication')
                ->setFormat('dd/MM/yyyy'),
        ];
    }
}
