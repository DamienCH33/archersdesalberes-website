<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class AlbumCrudController extends AbstractCrudController
{
    public const UPLOAD_DIR = 'uploads/albums';

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Album::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Album')
            ->setEntityLabelInPlural('Albums')
            ->setPageTitle('index', 'Liste des albums')
            ->setPageTitle('new', 'Créer un album')
            ->setPageTitle('edit', fn (Album $album): string => sprintf('Modifier « %s »', $album->getTitle()))
            ->setPageTitle('detail', 'Détails de l\'album')
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
                fn (Action $a): Action => $a->setLabel('Créer un album')->setIcon('fa fa-plus')
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
                fn (Action $a): Action => $a->setLabel('Créer')
            )
            ->update(
                Crud::PAGE_EDIT,
                Action::SAVE_AND_RETURN,
                fn (Action $a): Action => $a->setLabel('Enregistrer les modifications')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre')
            ->setRequired(true)
            ->setHelp('Le nom de l\'album (ex : "Championnat régional 2024")');

        yield SlugField::new('slug', 'URL')
            ->setTargetFieldName('title')
            ->setHelp('Généré automatiquement depuis le titre')
            ->hideOnIndex();

        yield ImageField::new('coverImage', 'Image de couverture')
            ->setBasePath(self::UPLOAD_DIR)
            ->setUploadDir('public/'.self::UPLOAD_DIR)
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[slug]-[randomhash].[extension]')
            ->setRequired(false)
            ->setHelp('Photo affichée en vignette sur la page galerie');

        yield BooleanField::new('isPublished', 'Publié')
            ->setHelp('Décochez pour cacher l\'album du site public')
            ->renderAsSwitch(true);

        yield AssociationField::new('photos', 'Nombre de photos')
            ->hideOnForm()
            ->formatValue(fn ($value, Album $album): string => count($album->getPhotos()).' photo(s)');

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    public function persistEntity(EntityManagerInterface $em, object $entityInstance): void
    {
        if ($entityInstance instanceof Album && !$entityInstance->getSlug()) {
            $slug = strtolower($this->slugger->slug($entityInstance->getTitle()));
            $entityInstance->setSlug($slug);
        }

        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, object $entityInstance): void
    {
        if ($entityInstance instanceof Album && !$entityInstance->getSlug()) {
            $slug = strtolower($this->slugger->slug($entityInstance->getTitle()));
            $entityInstance->setSlug($slug);
        }

        parent::updateEntity($em, $entityInstance);
    }
}
