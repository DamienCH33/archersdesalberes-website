<?php

namespace App\Factory;

use App\Entity\Article;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Article>
 */
final class ArticleFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Article::class;
    }

    protected function defaults(): array
    {
        $categories = ['podium', 'evenement', 'club', 'info', 'photos'];
        $title = self::faker()->sentence(6);

        return [
            'title' => $title,
            'slug' => (new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($title)->lower(),
            'content' => self::faker()->paragraphs(4, true),
            'category' => self::faker()->randomElement($categories),
            'isPublished' => true,
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', 'now')),
            'createdBy' => UserFactory::new(),
        ];
    }

    public function published(): self
    {
        return $this->with(['isPublished' => true]);
    }

    public function category(string $category): self
    {
        return $this->with(['category' => $category]);
    }

    public function podium(): self
    {
        return $this->category('podium')->with([
            'title' => self::faker()->randomElement([
                'Magnifique performance au championnat régional',
                'Double podium pour nos archers !',
                'Une médaille d’or bien méritée',
                'Résultats exceptionnels ce week-end',
                'Nos jeunes brillent en compétition',
            ]),
        ]);
    }

    public function evenement(): self
    {
        return $this->category('evenement')->with([
            'title' => self::faker()->randomElement([
                'Portes ouvertes du club',
                'Stage initiation vacances',
                'Tournoi annuel du club',
                'Journée découverte du tir à l’arc',
                'Stage perfectionnement adultes',
            ]),
        ]);
    }

    public function club(): self
    {
        return $this->category('club')->with([
            'title' => self::faker()->randomElement([
                'Nouvelle saison sportive',
                'Reprise des entraînements',
                'Vie du club et projets à venir',
                'Assemblée générale annuelle',
            ]),
        ]);
    }

    public function info(): self
    {
        return $this->category('info')->with([
            'title' => self::faker()->randomElement([
                'Informations importantes pour les licenciés',
                'Mise à jour des horaires',
                'Nouveaux équipements disponibles',
                'Rappel des règles de sécurité',
            ]),
        ]);
    }

    public function photos(): self
    {
        return $this->category('photos')->with([
            'title' => self::faker()->randomElement([
                'Album : Compétition régionale',
                'Photos : Stage d’été',
                'Galerie : Vie du club',
                'Album : Entraînements',
            ]),
        ]);
    }
}
