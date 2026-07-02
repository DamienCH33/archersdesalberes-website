<?php

declare(strict_types=1);

namespace App\Controller\Admin\Crud;

use App\Entity\Tarif;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Tarif>
 */
class TarifCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tarif::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tarif')
            ->setEntityLabelInPlural('Tarification')
            ->setDefaultSort(['category' => 'ASC', 'position' => 'ASC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Tarification');
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('category', 'Catégorie')->setChoices([
            'Adultes' => 'adultes',
            'Jeunes' => 'jeunes',
            'Licence découverte' => 'decouverte',
            'Matériel' => 'materiel',
        ]);
        yield TextField::new('label', 'Intitulé');
        yield MoneyField::new('price', 'Montant')->setCurrency('EUR')->setStoredAsCents(false);
        yield BooleanField::new('featured', 'Prix principal (gros affichage)');
        yield IntegerField::new('position', 'Ordre');
    }
}
