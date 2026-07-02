<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\AlbumFactory;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AlbumRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): AlbumRepository
    {
        return self::getContainer()->get(AlbumRepository::class);
    }

    public function testFindPublishedReturnsOnlyPublished(): void
    {
        self::bootKernel();

        AlbumFactory::new()->many(3)->create();
        AlbumFactory::new()->unpublished()->many(2)->create();

        $published = $this->repository()->findPublished();

        self::assertCount(3, $published);
        foreach ($published as $album) {
            self::assertTrue($album->isPublished());
        }
    }

    public function testCountPublished(): void
    {
        self::bootKernel();

        AlbumFactory::new()->many(4)->create();
        AlbumFactory::new()->unpublished()->create();

        self::assertSame(4, $this->repository()->countPublished());
    }

    public function testFindLatestPublishedRespectsLimit(): void
    {
        self::bootKernel();

        AlbumFactory::new()->many(8)->create();

        self::assertCount(3, $this->repository()->findLatestPublished(3));
    }

    public function testFindOnePublishedBySlug(): void
    {
        self::bootKernel();

        AlbumFactory::createOne(['slug' => 'album-2025', 'isPublished' => true]);

        $album = $this->repository()->findOnePublishedBySlug('album-2025');

        self::assertNotNull($album);
        self::assertSame('album-2025', $album->getSlug());
    }

    public function testFindOnePublishedBySlugReturnsNullWhenUnpublished(): void
    {
        self::bootKernel();

        AlbumFactory::new()->unpublished()->create(['slug' => 'cache']);

        self::assertNull($this->repository()->findOnePublishedBySlug('cache'));
    }
}
