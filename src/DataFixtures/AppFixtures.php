<?php

namespace App\DataFixtures;

use App\Factory\ArticleFactory;
use App\Factory\PartnerFactory;
use App\Factory\SettingFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        // ADMIN
        $admin = UserFactory::new()
            ->admin()
            ->with([
                'email' => 'admin@archers-alberes.fr',
                'firstName' => 'Admin',
                'lastName' => 'Club',
            ])
            ->create();

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin1234')
        );
        $manager->persist($admin);

        // DONNÉES STRUCTURELLES
        PartnerFactory::createDefaultPartners();
        SettingFactory::createDefaultSettings();

        // ARTICLES
        ArticleFactory::new()->podium()->many(8)->create();
        ArticleFactory::new()->evenement()->many(8)->create();
        ArticleFactory::new()->club()->many(6)->create();
        ArticleFactory::new()->info()->many(6)->create();
        ArticleFactory::new()->photos()->many(5)->create();
        ArticleFactory::new()->many(15)->create();

        $manager->flush();
    }
}
