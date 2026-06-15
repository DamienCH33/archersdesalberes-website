<?php

namespace App\Controller\Admin\Crud;

use App\Entity\TeamMember;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TeamMember::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Membre du bureau')
            ->setEntityLabelInPlural('Équipe')
            ->setPageTitle('index', 'Liste des membres du bureau')
            ->setPageTitle('new', 'Ajouter un membre')
            ->setPageTitle('edit', 'Modifier le membre')
            ->setPageTitle('detail', 'Détails du membre')
            ->setSearchFields(null)
            ->setDefaultSort(['displayOrder' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn(Action $a) => $a->setLabel('Ajouter un membre')->setIcon('fa fa-plus')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn(Action $a) => $a->setLabel('Modifier')->setIcon('fa fa-pencil')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn(Action $a) => $a->setLabel('Supprimer')->setIcon('fa fa-trash')
            )
            ->disable(Action::SAVE_AND_CONTINUE)
            ->update(
                Crud::PAGE_NEW,
                Action::SAVE_AND_RETURN,
                fn(Action $a) => $a->setLabel('Ajouter')
            )
            ->update(
                Crud::PAGE_EDIT,
                Action::SAVE_AND_RETURN,
                fn(Action $a) => $a->setLabel('Enregistrer les modifications')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('firstName', 'Prénom')
            ->setRequired(true)
            ->setColumns(6)
            ->setFormTypeOption('attr', ['placeholder' => 'Jean']);

        yield TextField::new('lastName', 'Nom')
            ->setRequired(true)
            ->setColumns(6)
            ->setFormTypeOption('attr', ['placeholder' => 'Dupont']);

        yield TextField::new('role', 'Fonction')
            ->setRequired(true)
            ->setHelp('Rôle dans le bureau (ex: Président, Trésorière, Secrétaire)')
            ->setFormTypeOption('attr', ['placeholder' => 'Président']);

        yield ImageField::new('avatar', 'Photo')
            ->setBasePath('uploads/team')
            ->setUploadDir('public/uploads/team')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('Photo du membre (optionnelle)');

        yield IntegerField::new('displayOrder', 'Ordre d\'affichage')
            ->setHelp('Plus le chiffre est petit, plus le membre apparaît en premier (1, 2, 3...)')
            ->setColumns(3)
            ->setFormTypeOption('attr', ['placeholder' => '1', 'min' => 0]);

        yield DateTimeField::new('createdAt', 'Ajouté le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy');
    }
}
