<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Tarif;
use PHPUnit\Framework\TestCase;

final class TarifTest extends TestCase
{
    public function testDefaults(): void
    {
        $tarif = new Tarif();

        self::assertNull($tarif->getId());
        self::assertFalse($tarif->isFeatured());
        self::assertSame(0, $tarif->getPosition());
    }

    public function testSettersAreFluentAndStoreValues(): void
    {
        $tarif = (new Tarif())
            ->setCategory('adultes')
            ->setLabel('Cotisation adulte')
            ->setPrice('120.00')
            ->setFeatured(true);

        self::assertSame('adultes', $tarif->getCategory());
        self::assertSame('Cotisation adulte', $tarif->getLabel());
        self::assertSame('120.00', $tarif->getPrice());
        self::assertTrue($tarif->isFeatured());
    }
}
