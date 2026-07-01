<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ClubStat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClubStatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ClubStat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Chiffre clé')
            ->setEntityLabelInPlural('Chiffres clés')
            ->setPageTitle('index', 'Liste des chiffres clés')
            ->setPageTitle('new', 'Ajouter un chiffre clé')
            ->setPageTitle('edit', 'Modifier le chiffre clé')
            ->setPageTitle('detail', 'Détails du chiffre clé')
            ->setSearchFields(null)
            ->setDefaultSort(['statKey' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $a): Action => $a->setLabel('Ajouter')->setIcon('fa fa-plus')
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
        yield TextField::new('label', 'Libellé')
            ->setRequired(true)
            ->setHelp('Texte affiché sur le site')
            ->setFormTypeOption('attr', ['placeholder' => 'Membres actifs']);

        yield IntegerField::new('statValue', 'Valeur')
            ->setRequired(true)
            ->setHelp('Le chiffre à afficher')
            ->setColumns(3)
            ->setFormTypeOption('attr', ['placeholder' => '127']);

        yield TextField::new('statKey', 'Clé')
            ->hideOnForm()
            ->hideOnIndex();

        yield DateTimeField::new('updatedAt', 'Dernière modification')
            ->onlyOnIndex()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, object $entityInstance): void
    {
        /** @var ClubStat $entityInstance */
        if (!$entityInstance->getStatKey()) {
            $entityInstance->setStatKey($this->generateStatKey($entityInstance->getLabel()));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, object $entityInstance): void
    {
        /** @var ClubStat $entityInstance */
        if (!$entityInstance->getStatKey()) {
            $entityInstance->setStatKey($this->generateStatKey($entityInstance->getLabel()));
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function generateStatKey(string $label): string
    {
        $key = strtolower($label);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        return trim((string) $key, '_');
    }
}
