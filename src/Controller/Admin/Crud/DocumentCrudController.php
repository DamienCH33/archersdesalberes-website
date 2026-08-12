<?php

declare(strict_types=1);

namespace App\Controller\Admin\Crud;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * @extends AbstractCrudController<Document>
 */
class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Document')
            ->setEntityLabelInPlural('Documents PDF')
            ->setPageTitle('index', 'Documents téléchargeables')
            ->setPageTitle('edit', 'Remplacer le PDF')
            ->setHelp('index', 'Clique sur « Remplacer » puis choisis un nouveau fichier PDF. Les documents sont fixes, tu ne peux que remplacer leur contenu.')
            ->setDefaultSort(['label' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // On retire la création et la suppression : les 4 documents sont fixes
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $a): Action => $a->setLabel('Remplacer le PDF')->setIcon('fa fa-upload')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        // Le nom : affiché mais non modifiable (document fixe)
        yield TextField::new('label', 'Document')
            ->setFormTypeOption('disabled', true);

        // Le fichier PDF : le seul champ vraiment modifiable
        yield TextField::new('file', 'Nouveau fichier PDF')
            ->setFormType(VichFileType::class)
            ->setFormTypeOptions([
                'allow_delete' => false,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('Choisis un fichier PDF (max 10 Mo). Il remplacera l\'ancien.');

        yield TextField::new('filename', 'Fichier actuel')
            ->onlyOnIndex();

        yield DateTimeField::new('updatedAt', 'Dernière mise à jour')
            ->onlyOnIndex()
            ->setFormat('dd/MM/yyyy HH:mm');
    }
}
