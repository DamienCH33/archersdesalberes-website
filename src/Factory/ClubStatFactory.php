<?php

declare(strict_types=1);

// src/Factory/ClubStatFactory.php

namespace App\Factory;

use App\Entity\ClubStat;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ClubStat>
 */
final class ClubStatFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return ClubStat::class;
    }

    protected function defaults(): array
    {
        return [
            'statKey' => self::faker()->unique()->slug(2),
            'statValue' => self::faker()->numberBetween(10, 200),
            'label' => self::faker()->words(3, true),
        ];
    }

    /**
     * Créer les statistiques par défaut du club.
     */
    public static function createDefaultStats(): void
    {
        $stats = [
            ['key' => 'membres_actifs', 'value' => 127, 'label' => 'Membres actifs'],
            ['key' => 'annees_existence', 'value' => 39, 'label' => 'Années d\'existence'],
            ['key' => 'competitions_an', 'value' => 24, 'label' => 'Compétitions par an'],
            ['key' => 'podiums_annee', 'value' => 15, 'label' => 'Podiums cette année'],
        ];

        foreach ($stats as $stat) {
            self::new()->create([
                'statKey' => $stat['key'],
                'statValue' => $stat['value'],
                'label' => $stat['label'],
            ]);
        }
    }
}
