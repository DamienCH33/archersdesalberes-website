<?php

declare(strict_types=1);

namespace App\Controller\Admin\Crud;

use App\Entity\Setting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Paramètre')
            ->setEntityLabelInPlural('Paramètres')
            ->setPageTitle('index', 'Liste des paramètres')
            ->setPageTitle('new', 'Ajouter un paramètre')
            ->setPageTitle('edit', 'Modifier le paramètre')
            ->setPageTitle('detail', 'Détails du paramètre')
            ->setSearchFields(null)
            ->setDefaultSort(['settingKey' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $a): Action => $a->setLabel('Ajouter un paramètre')->setIcon('fa fa-plus')
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
        yield TextField::new('settingKey', 'Clé')
            ->setRequired(true)
            ->setHelp('Identifiant unique du paramètre (ex: contact_email, facebook_url)')
            ->setFormTypeOption('attr', ['placeholder' => 'contact_email']);

        yield TextareaField::new('settingValue', 'Valeur')
            ->setRequired(true)
            ->setHelp('Contenu du paramètre')
            ->setFormTypeOption('attr', ['placeholder' => 'contact@club.fr', 'rows' => 3]);

        yield TextField::new('description', 'Description')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('Description pour faciliter la compréhension du paramètre')
            ->setFormTypeOption('attr', ['placeholder' => 'Email de contact affiché sur le site']);

        yield DateTimeField::new('updatedAt', 'Dernière modification')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }
}
