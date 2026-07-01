<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Article;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Actualité')
            ->setEntityLabelInPlural('Actualités')
            ->setPageTitle('index', 'Liste des actualités')
            ->setPageTitle('new', 'Créer une actualité')
            ->setPageTitle('edit', 'Modifier l\'actualité')
            ->setPageTitle('detail', 'Détails de l\'actualité')
            ->setSearchFields(null)
            ->setDefaultSort(['publishedAt' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(5);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $a): Action => $a->setLabel('Créer une actualité')->setIcon('fa fa-plus')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $a): Action => $a->setLabel('Modifier')->setIcon('fa fa-pencil')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $a): Action => $a->setLabel('Supprimer')->setIcon('fa fa-trash')
            )
            ->disable(Action::SAVE_AND_CONTINUE)
            ->update(
                Crud::PAGE_NEW,
                Action::SAVE_AND_RETURN,
                fn (Action $a): Action => $a->setLabel('Enregistrer')
            )
            ->update(
                Crud::PAGE_EDIT,
                Action::SAVE_AND_RETURN,
                fn (Action $a): Action => $a->setLabel('Enregistrer les modifications')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        // -------- Panneau 1 : Informations principales --------
        yield FormField::addFieldset('Informations principales')
            ->setIcon('fa fa-info-circle')
            ->onlyOnForms();

        yield TextField::new('title', 'Titre')
            ->setRequired(true)
            ->setHelp('Le titre de l\'actualité');

        yield SlugField::new('slug', 'URL')
            ->setTargetFieldName('title')
            ->hideOnIndex()
            ->setHelp('Généré automatiquement depuis le titre');

        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices([
                '🏆 Podium' => 'podium',
                '🎉 Événement' => 'evenement',
                '📢 Vie du club' => 'club',
                'ℹ️ Information' => 'info',
                '📸 Album photos' => 'photos',
            ])
            ->setRequired(true)
            ->renderAsBadges([
                'podium' => 'success',
                'evenement' => 'primary',
                'club' => 'info',
                'info' => 'warning',
                'photos' => 'secondary',
            ]);

        // -------- Panneau 2 : Contenu --------
        yield FormField::addFieldset('Contenu')
            ->setIcon('fa fa-edit')
            ->onlyOnForms();

        yield TextEditorField::new('content', 'Contenu')
            ->hideOnIndex()
            ->setHelp('Contenu complet de l\'actualité');

        yield ImageField::new('coverImage', 'Image de couverture')
            ->setBasePath('uploads/articles')
            ->setUploadDir('public/uploads/articles')
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[randomhash].[extension]')
            ->setRequired(false)
            ->hideOnIndex();

        yield AssociationField::new('album', 'Album lié (optionnel)')
            ->setHelp('Si l\'actualité présente un album photo, sélectionnez-le ici')
            ->setRequired(false)
            ->hideOnIndex();

        // -------- Panneau 3 : Publication --------
        yield FormField::addFieldset('Publication')
            ->setIcon('fa fa-paper-plane')
            ->onlyOnForms();

        yield BooleanField::new('isPublished', 'Publié')
            ->setHelp('L\'article est-il visible sur le site ?')
            ->renderAsSwitch(true);

        yield DateTimeField::new('publishedAt', 'Date de publication')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setHelp('Date de publication sur le site');

        yield AssociationField::new('createdBy', 'Auteur')
            ->hideOnForm()
            ->formatValue(fn ($value) => $value ? $value->getFullName() : 'Système');

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');

        yield DateTimeField::new('updatedAt', 'Modifié le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    public function createEntity(string $entityFqcn): object
    {
        $article = new Article();

        if ($this->getUser() instanceof User) {
            $article->setCreatedBy($this->getUser());
        }

        return $article;
    }
}
