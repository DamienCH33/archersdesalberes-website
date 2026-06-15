<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Partner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class PartnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Partner::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Partenaire')
            ->setEntityLabelInPlural('Partenaires')
            ->setPageTitle('index', 'Liste des partenaires')
            ->setPageTitle('new', 'Ajouter un partenaire')
            ->setPageTitle('edit', 'Modifier le partenaire')
            ->setPageTitle('detail', 'Détails du partenaire')
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
                fn(Action $a) => $a->setLabel('Ajouter un partenaire')->setIcon('fa fa-plus')
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
        yield TextField::new('name', 'Nom du partenaire')
            ->setRequired(true)
            ->setFormTypeOption('attr', ['placeholder' => 'Mairie d\'Argelès-sur-Mer']);

        yield ImageField::new('logo', 'Logo')
            ->setBasePath('uploads/partners')
            ->setUploadDir('public/uploads/partners')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->hideOnIndex()
            ->setHelp('Logo du partenaire (format PNG ou JPG recommandé)');

        yield UrlField::new('website', 'Site web')
            ->setRequired(false)
            ->setHelp('URL complète (ex: https://www.example.com)')
            ->setFormTypeOption('attr', ['placeholder' => 'https://www.example.com']);

        yield IntegerField::new('displayOrder', 'Ordre d\'affichage')
            ->setHelp('Plus le chiffre est petit, plus le partenaire apparaît en premier')
            ->setColumns(3)
            ->setFormTypeOption('attr', ['placeholder' => '1', 'min' => 0]);
    }
}
