<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PhotoCrudController extends AbstractCrudController
{
    public const UPLOAD_DIR = 'uploads/photos';

    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Photo')
            ->setEntityLabelInPlural('Photos')
            ->setPageTitle('index', 'Liste des photos')
            ->setPageTitle('new', 'Ajouter une photo')
            ->setPageTitle('edit', 'Modifier la photo')
            ->setPageTitle('detail', 'Détails de la photo')
            ->setSearchFields(null)
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn(Action $a) => $a->setLabel('Ajouter une photo')->setIcon('fa fa-plus')
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
        yield AssociationField::new('album', 'Album')
            ->setRequired(true)
            ->setHelp('Album dans lequel ranger cette photo');

        yield ImageField::new('path', 'Photo')
            ->setBasePath(self::UPLOAD_DIR)
            ->setUploadDir('public/' . self::UPLOAD_DIR)
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[randomhash].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->setHelp('Formats acceptés : JPG, PNG, WebP — Max 20 Mo')
            ->setFormTypeOptions([
                'attr' => ['accept' => 'image/jpeg,image/png,image/webp'],
            ]);

        yield TextField::new('filename', 'Nom du fichier')
            ->hideOnForm()
            ->hideOnIndex();

        yield TextareaField::new('caption', 'Légende')
            ->setRequired(false)
            ->setHelp('Description affichée sous la photo (optionnel)')
            ->hideOnIndex();

        yield IntegerField::new('displayOrder', 'Ordre d\'affichage')
            ->setHelp('Plus le chiffre est petit, plus la photo apparaît tôt dans l\'album')
            ->setColumns(3)
            ->setFormTypeOption('attr', ['min' => 0]);

        yield DateTimeField::new('createdAt', 'Ajoutée le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (
            $entityInstance instanceof Photo
            && $entityInstance->getPath()
            && !$entityInstance->getFilename()
        ) {
            $entityInstance->setFilename($entityInstance->getPath());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (
            $entityInstance instanceof Photo
            && $entityInstance->getPath()
            && !$entityInstance->getFilename()
        ) {
            $entityInstance->setFilename($entityInstance->getPath());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
