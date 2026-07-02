<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Factory\ArticleFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ArticleControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPublishedArticleRenders(): void
    {
        $client = static::createClient();
        ArticleFactory::createOne([
            'slug' => 'mon-article',
            'isPublished' => true,
        ]);

        $client->request('GET', '/article/mon-article');

        self::assertResponseIsSuccessful();
    }

    public function testUnpublishedArticleReturns404(): void
    {
        $client = static::createClient();
        ArticleFactory::createOne([
            'slug' => 'brouillon',
            'isPublished' => false,
        ]);

        $client->request('GET', '/article/brouillon');

        self::assertResponseStatusCodeSame(404);
    }

    public function testUnknownArticleReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/article/inexistant');

        self::assertResponseStatusCodeSame(404);
    }

    public function testActualitesFilteredByCategory(): void
    {
        $client = static::createClient();
        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'podium'])->many(3)->create();
        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'club'])->many(2)->create();

        $client->request('GET', '/actualites?category=podium');

        self::assertResponseIsSuccessful();
    }

    public function testActualitesPaginationSecondPage(): void
    {
        $client = static::createClient();
        ArticleFactory::new()->with(['isPublished' => true])->many(15)->create();

        $client->request('GET', '/actualites?page=2');

        self::assertResponseIsSuccessful();
    }
}
