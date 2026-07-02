<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use App\Entity\Photo;
use PHPUnit\Framework\TestCase;

final class AlbumTest extends TestCase
{
    public function testDefaults(): void
    {
        $album = new Album();

        self::assertNull($album->getId());
        self::assertTrue($album->isPublished());
        self::assertCount(0, $album->getPhotos());
        self::assertInstanceOf(\DateTimeImmutable::class, $album->getCreatedAt());
    }

    public function testAddPhotoSetsBothSidesOfRelation(): void
    {
        $album = new Album();
        $photo = new Photo();

        $album->addPhoto($photo);

        self::assertCount(1, $album->getPhotos());
        self::assertSame($album, $photo->getAlbum());
    }

    public function testAddPhotoIsIdempotent(): void
    {
        $album = new Album();
        $photo = new Photo();

        $album->addPhoto($photo);
        $album->addPhoto($photo);

        self::assertCount(1, $album->getPhotos());
    }

    public function testRemovePhotoDetachesRelation(): void
    {
        $album = new Album();
        $photo = new Photo();

        $album->addPhoto($photo);
        $album->removePhoto($photo);

        self::assertCount(0, $album->getPhotos());
        self::assertNull($photo->getAlbum());
    }

    public function testToStringReturnsTitle(): void
    {
        $album = (new Album())->setTitle('Compétition 2025');

        self::assertSame('Compétition 2025', (string) $album);
    }
}
