<?php

declare(strict_types=1);

namespace App\Controller\Admin\Crud;

use App\Entity\ClubHistory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClubHistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ClubHistory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Histoire du club')
            ->setEntityLabelInPlural('Histoire du club')
            ->setPageTitle(Crud::PAGE_INDEX, 'Histoire du club');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield IntegerField::new('foundingYear', 'Année de création')
            ->setHelp('Les "X ans de passion" sont calculés automatiquement à partir de cette année.');
        yield TextEditorField::new('content', 'Texte (rédigé par Didier)')->hideOnIndex();
    }
}
