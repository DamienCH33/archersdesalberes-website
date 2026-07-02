<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): UserRepository
    {
        return self::getContainer()->get(UserRepository::class);
    }

    public function testFindOneByEmail(): void
    {
        self::bootKernel();

        UserFactory::new()->with(['email' => 'admin@archers.fr'])->create();

        $user = $this->repository()->findOneByEmail('admin@archers.fr');

        self::assertNotNull($user);
        self::assertSame('admin@archers.fr', $user->getEmail());
    }

    public function testFindOneByEmailReturnsNullWhenAbsent(): void
    {
        self::bootKernel();

        self::assertNull($this->repository()->findOneByEmail('inconnu@archers.fr'));
    }

    public function testFindAllAdmins(): void
    {
        self::bootKernel();

        UserFactory::new()->admin()->many(3)->create();

        // getRoles() force ROLE_ADMIN, donc tous les users sont admins.
        self::assertGreaterThanOrEqual(3, count($this->repository()->findAllAdmins()));
    }

    public function testCountAdmins(): void
    {
        self::bootKernel();

        UserFactory::new()->admin()->many(2)->create();

        self::assertGreaterThanOrEqual(2, $this->repository()->countAdmins());
    }
}
