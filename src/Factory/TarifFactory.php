<?php

namespace App\Factory;

use App\Entity\Tarif;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Tarif>
 */
final class TarifFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Tarif::class;
    }

    protected function defaults(): array
    {
        return [
            'category' => self::faker()->randomElement(['jeunes', 'adultes', 'famille']),
            'label' => self::faker()->words(3, true),
            'price' => (string) self::faker()->numberBetween(80, 200).'.00',
            'featured' => false,
            'position' => self::faker()->numberBetween(0, 10),
        ];
    }

    public function featured(): self
    {
        return $this->with(['featured' => true]);
    }
}
