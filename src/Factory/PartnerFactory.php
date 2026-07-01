<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Partner;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Partner>
 */
final class PartnerFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Partner::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'website' => self::faker()->optional()->url(),
            'logo' => self::faker()->imageUrl(200, 100, 'business'),
            'displayOrder' => self::faker()->numberBetween(1, 10),
        ];
    }

    /**
     * Créer les partenaires par défaut.
     */
    public static function createDefaultPartners(): void
    {
        $partners = [
            ['name' => 'Mairie d\'Argelès-sur-Mer', 'order' => 1],
            ['name' => 'Région Occitanie', 'order' => 2],
            ['name' => 'FFTA - Fédération Française de Tir à l\'Arc', 'order' => 3],
            ['name' => 'Decathlon Perpignan', 'order' => 4],
        ];

        foreach ($partners as $partner) {
            self::new()->create([
                'name' => $partner['name'],
                'displayOrder' => $partner['order'],
            ]);
        }
    }
}
