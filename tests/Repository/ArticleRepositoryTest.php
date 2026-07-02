<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\ArticleFactory;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ArticleRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): ArticleRepository
    {
        return self::getContainer()->get(ArticleRepository::class);
    }

    public function testFindPublishedReturnsOnlyPublished(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true])->create();
        ArticleFactory::new()->with(['isPublished' => true])->create();
        ArticleFactory::new()->with(['isPublished' => false])->create();

        $published = $this->repository()->findPublished();

        self::assertCount(2, $published);
        foreach ($published as $article) {
            self::assertTrue($article->isPublished());
        }
    }

    public function testCountPublished(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true])->many(3)->create();
        ArticleFactory::new()->with(['isPublished' => false])->create();

        self::assertSame(3, $this->repository()->countPublished());
    }

    public function testFindPublishedByCategory(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'club'])->many(2)->create();
        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'info'])->create();

        $result = $this->repository()->findPublishedByCategory('club');

        self::assertCount(2, $result);
        foreach ($result as $article) {
            self::assertSame('club', $article->getCategory());
        }
    }

    public function testFindPublishedByCategoryRespectsLimit(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'club'])->many(5)->create();

        $result = $this->repository()->findPublishedByCategory('club', 2);

        self::assertCount(2, $result);
    }

    public function testFindLatestPublishedRespectsLimit(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true])->many(10)->create();

        $result = $this->repository()->findLatestPublished(4);

        self::assertCount(4, $result);
    }

    public function testFindOnePublishedBySlug(): void
    {
        self::bootKernel();

        $article = ArticleFactory::new()
            ->with(['isPublished' => true, 'slug' => 'mon-article-test'])
            ->create();

        $found = $this->repository()->findOnePublishedBySlug('mon-article-test');

        self::assertNotNull($found);
        self::assertSame('mon-article-test', $found->getSlug());
    }

    public function testFindOnePublishedBySlugReturnsNullForUnpublished(): void
    {
        self::bootKernel();

        ArticleFactory::new()
            ->with(['isPublished' => false, 'slug' => 'article-brouillon'])
            ->create();

        self::assertNull($this->repository()->findOnePublishedBySlug('article-brouillon'));
    }

    public function testCountByCategory(): void
    {
        self::bootKernel();

        ArticleFactory::new()->with(['isPublished' => true, 'category' => 'evenement'])->many(2)->create();

        self::assertSame(2, $this->repository()->countByCategory('evenement'));
    }
}
