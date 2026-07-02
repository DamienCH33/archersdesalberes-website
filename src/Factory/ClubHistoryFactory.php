<?php

namespace App\Factory;

use App\Entity\ClubHistory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ClubHistory>
 */
final class ClubHistoryFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return ClubHistory::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => 'Notre histoire',
            'foundingYear' => 1991,
            'content' => self::faker()->paragraphs(3, true),
        ];
    }
}
