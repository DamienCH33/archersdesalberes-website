<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'roles' => ['ROLE_USER'],
            'password' => '$2y$13$dummyhashedpassword',
        ];
    }

    public function admin(): self
    {
        return $this->with([
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    /**
     * Créer un admin
     */
    public static function createAdmin(string $email, string $plainPassword): User
    {
        return self::new()
            ->admin()
            ->with([
                'email' => $email,
                'password' => $plainPassword, // ⚠️ à hasher avant usage réel
            ])
            ->create();
    }
}
