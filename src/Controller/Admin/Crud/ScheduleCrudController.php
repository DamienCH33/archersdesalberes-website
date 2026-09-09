<?php

declare(strict_types=1);

namespace App\Controller\Admin\Crud;

use App\Entity\Schedule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

/**
 * @extends AbstractCrudController<Schedule>
 */
class ScheduleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Schedule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Créneau')
            ->setEntityLabelInPlural('Horaires d\'entraînement')
            ->setPageTitle('index', 'Horaires d\'entraînement')
            ->setPageTitle('new', 'Ajouter un créneau')
            ->setPageTitle('edit', 'Modifier le créneau')
            ->setHelp('index', 'Gère ici les créneaux affichés sur la page Horaires. Pour un créneau qui change selon la saison (ex : vendredi), crée deux créneaux : un « Hiver » et un « Été ».')
            ->setDefaultSort(['displayOrder' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $a): Action => $a->setLabel('Ajouter un créneau')->setIcon('fa fa-plus')
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
            );
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices(Schedule::CATEGORIES)
            ->setHelp('Le groupe concerné par ce créneau');

        yield ChoiceField::new('day', 'Jour')
            ->setChoices(Schedule::DAYS);

        yield ChoiceField::new('season', 'Saison')
            ->setChoices(Schedule::SEASONS)
            ->setHelp('« Toute l\'année » sauf si l\'horaire change entre l\'hiver et l\'été');

        yield TimeField::new('startTime', 'Heure de début')
            ->setFormat('HH:mm');

        yield TimeField::new('endTime', 'Heure de fin')
            ->setFormat('HH:mm');

        yield TextField::new('winterLocation', 'Lieu en hiver')
            ->setRequired(false)
            ->setHelp('Ex : en salle')
            ->hideOnIndex();

        yield TextField::new('summerLocation', 'Lieu en été')
            ->setRequired(false)
            ->setHelp('Ex : sur le terrain')
            ->hideOnIndex();

        yield IntegerField::new('displayOrder', 'Ordre d\'affichage')
            ->setHelp('Plus le chiffre est petit, plus le créneau apparaît en premier')
            ->setColumns(3);
    }
}
