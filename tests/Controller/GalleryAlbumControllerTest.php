<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Factory\AlbumFactory;
use App\Factory\PhotoFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class GalleryAlbumControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPublishedAlbumRenders(): void
    {
        $client = static::createClient();
        AlbumFactory::createOne(['slug' => 'sortie-2025', 'isPublished' => true]);

        $client->request('GET', '/gallery/sortie-2025');

        self::assertResponseIsSuccessful();
    }

    public function testUnpublishedAlbumReturns404(): void
    {
        $client = static::createClient();
        AlbumFactory::new()->unpublished()->create(['slug' => 'album-cache']);

        $client->request('GET', '/gallery/album-cache');

        self::assertResponseStatusCodeSame(404);
    }

    public function testUnknownAlbumReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/gallery/slug-inexistant');

        self::assertResponseStatusCodeSame(404);
    }

    public function testPhotoInPublishedAlbumRenders(): void
    {
        $client = static::createClient();
        $album = AlbumFactory::createOne(['isPublished' => true]);
        $photo = PhotoFactory::createOne(['album' => $album]);

        $client->request('GET', '/photo/'.$photo->getId());

        self::assertResponseIsSuccessful();
    }

    public function testPhotoInUnpublishedAlbumReturns404(): void
    {
        $client = static::createClient();
        $album = AlbumFactory::new()->unpublished()->create();
        $photo = PhotoFactory::createOne(['album' => $album]);

        $client->request('GET', '/photo/'.$photo->getId());

        self::assertResponseStatusCodeSame(404);
    }
}
