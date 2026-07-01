<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PublicPagesTest extends WebTestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function publicUrls(): iterable
    {
        yield 'accueil' => ['/'];
        yield 'actualités' => ['/actualites'];
        yield 'adhésion' => ['/adhesion'];
        yield 'horaires' => ['/horaires'];
        yield 'contact' => ['/contact'];
        yield 'histoire' => ['/history'];
        yield 'galerie' => ['/gallery'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicUrls')]
    public function testPageRenders(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }
}
