<?php

declare(strict_types=1);

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
    public function testAnonymousCannotAccessAdmin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        self::assertResponseRedirects();
    }
}
