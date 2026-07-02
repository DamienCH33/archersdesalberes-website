<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminAccessTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAnonymousIsRedirectedFromAdmin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        self::assertResponseRedirects();
    }

    public function testAnonymousIsRedirectedFromAdminEntityList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/article');

        self::assertResponseRedirects();
    }

    public function testAuthenticatedAdminReachesDashboard(): void
    {
        $client = static::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin');

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function adminEntityRoutes(): iterable
    {
        yield 'articles' => ['/admin/article'];
        yield 'albums' => ['/admin/album'];
        yield 'tarifs' => ['/admin/tarif'];
        yield 'partenaires' => ['/admin/partner'];
        yield 'settings' => ['/admin/setting'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminEntityRoutes')]
    public function testAuthenticatedAdminReachesEntityList(string $url): void
    {
        $client = static::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }
}
