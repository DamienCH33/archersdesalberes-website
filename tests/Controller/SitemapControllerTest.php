<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class SitemapControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testSitemapIsAccessibleAndXml(): void
    {
        $client = self::createClient();
        $client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/xml');
    }

    public function testSitemapContainsStaticPages(): void
    {
        $client = self::createClient();
        $client->request('GET', '/sitemap.xml');

        $content = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('<urlset', $content);
        self::assertStringContainsString('/actualites', $content);
        self::assertStringContainsString('/gallery', $content);
    }
}
