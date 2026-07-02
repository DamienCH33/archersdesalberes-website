<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ClubHistory;
use PHPUnit\Framework\TestCase;

final class ClubHistoryTest extends TestCase
{
    public function testDefaults(): void
    {
        $history = new ClubHistory();

        self::assertNull($history->getId());
        self::assertSame('Notre histoire', $history->getTitle());
        self::assertSame(1991, $history->getFoundingYear());
    }

    public function testSettersStoreValues(): void
    {
        $history = (new ClubHistory())
            ->setTitle('Histoire du club')
            ->setFoundingYear(1985)
            ->setContent('Un long texte.');

        self::assertSame('Histoire du club', $history->getTitle());
        self::assertSame(1985, $history->getFoundingYear());
        self::assertSame('Un long texte.', $history->getContent());
    }

    public function testYearsOfPassionIsComputedFromFoundingYear(): void
    {
        $history = (new ClubHistory())->setFoundingYear(2000);

        $expected = (int) date('Y') - 2000;

        self::assertSame($expected, $history->getYearsOfPassion());
    }

    public function testToStringReturnsTitle(): void
    {
        $history = (new ClubHistory())->setTitle('Mon titre');

        self::assertSame('Mon titre', (string) $history);
    }
}
