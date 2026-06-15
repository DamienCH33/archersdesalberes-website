<?php

namespace App\Factory;

use App\Entity\TeamMember;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TeamMember>
 */
final class TeamMemberFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return TeamMember::class;
    }

    protected function defaults(): array
    {
        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'role' => self::faker()->randomElement([
                'Président',
                'Trésorier',
                'Secrétaire',
                'Entraîneur',
                'Responsable matériel',
            ]),
            'avatar' => self::faker()->imageUrl(200, 200, 'people'),
            'displayOrder' => self::faker()->numberBetween(1, 10),
        ];
    }

    /**
     * Créer l'équipe du bureau par défaut
     */
    public static function createDefaultTeam(): void
    {
        $team = [
            ['firstName' => 'Jean', 'lastName' => 'Dupont', 'role' => 'Président', 'order' => 1],
            ['firstName' => 'Marie', 'lastName' => 'Martin', 'role' => 'Trésorier', 'order' => 2],
            ['firstName' => 'Pierre', 'lastName' => 'Bernard', 'role' => 'Entraîneur', 'order' => 3],
            ['firstName' => 'Sophie', 'lastName' => 'Lefebvre', 'role' => 'Secrétaire', 'order' => 4],
            ['firstName' => 'Lucas', 'lastName' => 'Moreau', 'role' => 'Responsable matériel', 'order' => 5],
        ];

        foreach ($team as $member) {
            self::new()->create([
                'firstName' => $member['firstName'],
                'lastName' => $member['lastName'],
                'role' => $member['role'],
                'displayOrder' => $member['order'],
            ]);
        }
    }
}
