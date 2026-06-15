<?php
// src/Factory/SettingFactory.php

namespace App\Factory;

use App\Entity\Setting;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Setting>
 */
final class SettingFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Setting::class;
    }

    protected function defaults(): array
    {
        return [
            'settingKey' => self::faker()->unique()->slug(2),
            'settingValue' => self::faker()->sentence(),
            'description' => self::faker()->optional()->sentence(),
        ];
    }

    /**
     * Créer les paramètres par défaut du site
     */
    public static function createDefaultSettings(): void
    {
        $settings = [
            [
                'key' => 'contact_email',
                'value' => 'archersdesalberes@gmail.com',
                'description' => 'Email de contact du club',
            ],
            [
                'key' => 'contact_phone',
                'value' => '06 02 23 72 58',
                'description' => 'Téléphone de contact du club',
            ],
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/archersdesalberes',
                'description' => 'Page Facebook du club',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/archersdesalberes',
                'description' => 'Page Instagram du club',
            ],
            [
                'key' => 'address',
                'value' => 'Gymnase Municipal, Avenue du Vallespir, 66700 Argelès-sur-Mer',
                'description' => 'Adresse du club',
            ],
            [
                'key' => 'hero_bg_color',
                'value' => '#E8F5E9',
                'description' => 'Couleur de fond du hero',
            ],
        ];

        foreach ($settings as $setting) {
            self::new()->create([
                'settingKey' => $setting['key'],
                'settingValue' => $setting['value'],
                'description' => $setting['description'],
            ]);
        }
    }
}
