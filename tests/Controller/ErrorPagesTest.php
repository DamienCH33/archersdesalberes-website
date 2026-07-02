<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ErrorPagesTest extends WebTestCase
{
    public function testNotFoundPageReturns404(): void
    {
        $client = static::createClient();
        // Désactive le throw pour capturer la vraie réponse d'erreur.
        $client->catchExceptions(true);

        $client->request('GET', '/cette-page-nexiste-pas');

        self::assertResponseStatusCodeSame(404);
    }

    public function testNotFoundOnUnknownArticleSlug(): void
    {
        $client = static::createClient();
        $client->catchExceptions(true);

        $client->request('GET', '/article/slug-totalement-inconnu');

        self::assertResponseStatusCodeSame(404);
    }
}
