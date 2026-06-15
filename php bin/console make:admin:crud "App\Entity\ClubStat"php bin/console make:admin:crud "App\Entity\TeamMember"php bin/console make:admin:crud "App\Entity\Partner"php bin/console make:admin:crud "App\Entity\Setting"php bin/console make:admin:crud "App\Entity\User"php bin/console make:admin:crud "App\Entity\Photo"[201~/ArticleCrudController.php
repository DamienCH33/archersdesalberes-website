<?php

namespace Php\Bin\Console\Make:Admin:Crud\"App\Entity\ClubStat"Php\Bin\Console\Make:Admin:Crud\"App\Entity\TeamMember"Php\Bin\Console\Make:Admin:Crud\"App\Entity\Partner"Php\Bin\Console\Make:Admin:Crud\"App\Entity\Setting"Php\Bin\Console\Make:Admin:Crud\"App\Entity\User"Php\Bin\Console\Make:Admin:Crud\"App\Entity\Photo"[201~;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
