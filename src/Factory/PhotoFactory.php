<?php

namespace App\Factory;

use App\Entity\Photo;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Photo>
 */
final class PhotoFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Photo::class;
    }

    protected function defaults(): array
    {
        return [
            'filename' => self::faker()->word().'.jpg',
            'path' => self::faker()->word().'.jpg',
            'caption' => self::faker()->optional()->sentence(),
            'displayOrder' => self::faker()->numberBetween(0, 20),
            'album' => AlbumFactory::new(),
        ];
    }
}
