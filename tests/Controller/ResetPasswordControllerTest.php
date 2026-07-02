<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ResetPasswordControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testResetPasswordController(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();

        // Utilisateur de test (first_name / last_name obligatoires)
        $user = (new User())
            ->setEmail('me@example.com')
            ->setPassword('a-test-password-that-will-be-changed-later')
            ->setFirstName('Test')
            ->setLastName('User');
        $em->persist($user);
        $em->flush();

        // Page de demande de réinitialisation
        $client->request('GET', '/reset-password');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Mot de passe oublié');

        // Soumission du formulaire de demande
        $client->submitForm('Envoyer le lien', [
            'reset_password_request_form[email]' => 'me@example.com',
        ]);

        // L'email de réinitialisation est bien envoyé
        self::assertEmailCount(1);
        $messages = $this->getMailerMessages();
        self::assertCount(1, $messages);

        self::assertEmailAddressContains($messages[0], 'from', 'noreply@archersdesalberes.fr');
        self::assertEmailAddressContains($messages[0], 'to', 'me@example.com');

        self::assertResponseRedirects('/reset-password/check-email');

        // Page de confirmation d'envoi
        $client->followRedirect();
        self::assertPageTitleContains('Email de réinitialisation envoyé');

        // Récupération du lien de reset dans l'email
        $email = $messages[0]->toString();
        preg_match('#(/reset-password/reset/[a-zA-Z0-9]+)#', $email, $resetLink);

        self::assertNotEmpty($resetLink, 'Le lien de réinitialisation est introuvable dans l\'email.');

        if (!isset($resetLink[1])) {
            self::fail('Lien de réinitialisation introuvable dans l\'email.');
        }

        // Le lien stocke le token en session puis redirige vers /reset-password/reset
        $client->request('GET', $resetLink[1]);
        self::assertResponseRedirects('/reset-password/reset');
        $client->followRedirect();

        // Nouveau mot de passe conforme (min 12, PasswordStrength ;
        // NotCompromisedPassword est desactive en env test via validator.yaml)
        $newPassword = 'Zt7#vQ9r!Lk2Wp4Xy';

        $client->submitForm('Enregistrer le mot de passe', [
            'change_password_form[plainPassword][first]' => $newPassword,
            'change_password_form[plainPassword][second]' => $newPassword,
        ]);

        self::assertResponseRedirects('/login');

        // Verification que le nouveau mot de passe est bien enregistre (hashe)
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'me@example.com']);
        self::assertInstanceOf(User::class, $user);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        self::assertTrue($passwordHasher->isPasswordValid($user, $newPassword));
    }
}
