<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class PhotoCrudController extends AbstractCrudController
{
    public const UPLOAD_DIR = 'uploads/photos';

    public function __construct(
        private EntityManagerInterface $em,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {}

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
            ->setPageTitle('new', 'Ajouter des photos')
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
                fn(Action $a) => $a->setLabel('Ajouter des photos')->setIcon('fa fa-plus')
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
            ->setRequired(false)
            ->setHelp('Formats acceptés : JPG, PNG, WebP')
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

    /**
     * Surcharge de l'action "new" : formulaire d'upload multiple.
     */
    public function new(AdminContext $context): Response
    {
        $request = $context->getRequest();
        $albums = $this->em->getRepository(Album::class)->findBy([], ['createdAt' => 'DESC']);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('photo_batch', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }

            $album = $this->em->getRepository(Album::class)->find($request->request->get('album'));
            $files = $request->files->get('images', []);

            if (!$album) {
                $this->addFlash('danger', 'Choisis un album.');
            } elseif (!$files) {
                $this->addFlash('warning', 'Aucune photo sélectionnée.');
            } else {
                $dir = $this->getParameter('kernel.project_dir') . '/public/' . self::UPLOAD_DIR;
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                $order = (int) $this->em->getRepository(Photo::class)->count(['album' => $album]);
                $added = 0;

                foreach ($files as $file) {
                    if (!$file) {
                        continue;
                    }
                    $name = sprintf(
                        '%s-%s.%s',
                        date('Y-m-d'),
                        bin2hex(random_bytes(6)),
                        $file->guessExtension() ?: 'jpg'
                    );
                    $file->move($dir, $name);

                    $photo = (new Photo())
                        ->setAlbum($album)
                        ->setPath($name)
                        ->setFilename($name)
                        ->setDisplayOrder($order++);
                    $this->em->persist($photo);
                    ++$added;
                }

                $this->em->flush();
                $this->addFlash('success', $added . ' photo(s) ajoutée(s) à l\'album « ' . $album->getTitle() . ' ».');

                return $this->redirect(
                    $this->adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl()
                );
            }
        }

        return $this->render('admin/photo_batch.html.twig', [
            'albums' => $albums,
        ]);
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
