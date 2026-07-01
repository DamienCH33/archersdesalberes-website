<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GalleryControllerTest extends WebTestCase
{
    public function testGalleryPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/gallery');

        self::assertResponseIsSuccessful();
    }
}
