<?php

namespace App\Controller\Admin\Crud;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends AbstractCrudController<User>
 */
class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Administrateur')
            ->setEntityLabelInPlural('Administrateurs')
            ->setPageTitle('index', 'Liste des administrateurs')
            ->setPageTitle('new', 'Ajouter un administrateur')
            ->setPageTitle('edit', 'Modifier l\'administrateur')
            ->setPageTitle('detail', 'Détails de l\'administrateur')
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
                fn (Action $a): Action => $a->setLabel('Ajouter un administrateur')->setIcon('fa fa-plus')
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
        // -------- Panneau 1 : Identité --------
        yield FormField::addFieldset('Identité')
            ->setIcon('fa fa-user')
            ->onlyOnForms();

        yield TextField::new('firstName', 'Prénom')
            ->setRequired(true)
            ->setColumns(6)
            ->setFormTypeOption('attr', ['placeholder' => 'Jean']);

        yield TextField::new('lastName', 'Nom')
            ->setRequired(true)
            ->setColumns(6)
            ->setFormTypeOption('attr', ['placeholder' => 'Dupont']);

        yield EmailField::new('email', 'Email')
            ->setRequired(true)
            ->setHelp('Adresse email de connexion')
            ->setFormTypeOption('attr', ['placeholder' => 'admin@archers-alberes.fr']);

        // -------- Panneau 2 : Sécurité --------
        yield FormField::addFieldset('Sécurité')
            ->setIcon('fa fa-lock')
            ->onlyOnForms();

        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->setHelp('Rôles de l\'utilisateur dans l\'application')
            ->renderExpanded()
            ->renderAsBadges();

        if (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            yield TextField::new('plainPassword', 'Mot de passe')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Minimum 8 caractères']],
                    'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => ['placeholder' => 'Retapez le mot de passe']],
                    'mapped' => false,
                ])
                ->setRequired(Crud::PAGE_NEW === $pageName)
                ->setHelp(Crud::PAGE_EDIT === $pageName
                    ? 'Laissez vide pour ne pas changer le mot de passe'
                    : 'Minimum 8 caractères');
        }

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    /**
     * @return FormBuilderInterface<mixed>
     */
    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        return $this->addPasswordEventListener($formBuilder);
    }

    /**
     * @return FormBuilderInterface<mixed>
     */
    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        return $this->addPasswordEventListener($formBuilder);
    }

    /**
     * @param FormBuilderInterface<mixed> $formBuilder
     *
     * @return FormBuilderInterface<mixed>
     */
    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }

            $plainPassword = $form->get('plainPassword')->getData();
            if (!$plainPassword) {
                return;
            }

            /** @var User $user */
            $user = $form->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        });
    }

    public function createEntity(string $entityFqcn): object
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        return $user;
    }
}
