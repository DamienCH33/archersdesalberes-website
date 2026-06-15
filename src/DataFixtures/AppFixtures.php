<?php

namespace App\DataFixtures;

use App\Factory\ArticleFactory;
use App\Factory\ClubStatFactory;
use App\Factory\PartnerFactory;
use App\Factory\SettingFactory;
use App\Factory\TeamMemberFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // ADMIN
        $admin = UserFactory::new()
            ->with([
                'email' => 'admin@archers-alberes.fr',
                'firstName' => 'Admin',
                'lastName' => 'Club',
                'roles' => ['ROLE_ADMIN'],
            ])
            ->create();

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin1234')
        );

        // DONNÉES STRUCTURELLES
        ClubStatFactory::createDefaultStats();
        TeamMemberFactory::createDefaultTeam();
        PartnerFactory::createDefaultPartners();
        SettingFactory::createDefaultSettings();

        // ARTICLES

        ArticleFactory::new()->podium()->many(8)->create();
        ArticleFactory::new()->evenement()->many(8)->create();
        ArticleFactory::new()->club()->many(6)->create();
        ArticleFactory::new()->info()->many(6)->create();
        ArticleFactory::new()->photos()->many(5)->create();

        // Articles random supplémentaires
        ArticleFactory::new()->many(15)->create();

        $manager->flush();

        echo "\n Fixtures chargées avec succès !\n";
    }
}
