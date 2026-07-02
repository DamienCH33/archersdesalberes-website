<?php

namespace App\Factory;

use App\Entity\Album;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Album>
 */
final class AlbumFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Album::class;
    }

    protected function defaults(): array
    {
        $title = self::faker()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => (new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($title)->lower(),
            'coverImage' => null,
            'isPublished' => true,
        ];
    }

    public function unpublished(): self
    {
        return $this->with(['isPublished' => false]);
    }
}
