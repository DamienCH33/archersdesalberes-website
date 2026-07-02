<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\ClubHistoryFactory;
use App\Repository\ClubHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ClubHistoryRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): ClubHistoryRepository
    {
        return self::getContainer()->get(ClubHistoryRepository::class);
    }

    public function testGetSingleReturnsNullWhenEmpty(): void
    {
        self::bootKernel();

        self::assertNull($this->repository()->getSingle());
    }

    public function testGetSingleReturnsTheEntity(): void
    {
        self::bootKernel();

        ClubHistoryFactory::createOne(['title' => 'Histoire test']);

        $history = $this->repository()->getSingle();

        self::assertNotNull($history);
        self::assertSame('Histoire test', $history->getTitle());
    }
}
