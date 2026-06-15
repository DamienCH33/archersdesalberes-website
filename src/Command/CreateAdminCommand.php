<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // EMAIL
        $emailQuestion = new Question('Email: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Email invalide.');
            return Command::FAILURE;
        }

        // Vérifie si existe déjà
        $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà.');
            return Command::FAILURE;
        }

        // PASSWORD
        $passwordQuestion = new Question('Password (min 6 caractères): ');
        $passwordQuestion->setHidden(true);
        $password = $helper->ask($input, $output, $passwordQuestion);

        if (!$password || strlen($password) < 6) {
            $io->error('Mot de passe trop court.');
            return Command::FAILURE;
        }

        // NOM
        $firstName = $helper->ask($input, $output, new Question('Prénom: '));
        $lastName = $helper->ask($input, $output, new Question('Nom: '));

        // USER
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName ?? 'Admin');
        $user->setLastName($lastName ?? 'User');
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin créé avec succès !');

        return Command::SUCCESS;
    }
}
