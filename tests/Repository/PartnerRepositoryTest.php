<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\PartnerFactory;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PartnerRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): PartnerRepository
    {
        return self::getContainer()->get(PartnerRepository::class);
    }

    public function testCountPartners(): void
    {
        self::bootKernel();

        PartnerFactory::new()->many(4)->create();

        self::assertSame(4, $this->repository()->countPartners());
    }

    public function testFindOneByName(): void
    {
        self::bootKernel();

        PartnerFactory::new()->with(['name' => 'Mairie de Sorède'])->create();

        $partner = $this->repository()->findOneByName('Mairie de Sorède');

        self::assertNotNull($partner);
        self::assertSame('Mairie de Sorède', $partner->getName());
    }

    public function testFindAllForDisplay(): void
    {
        self::bootKernel();

        PartnerFactory::new()->many(3)->create();

        self::assertCount(3, $this->repository()->findAllForDisplay());
    }

    public function testSearchByName(): void
    {
        self::bootKernel();

        PartnerFactory::new()->with(['name' => 'Région Occitanie'])->create();
        PartnerFactory::new()->with(['name' => 'Commerce local'])->create();

        $result = $this->repository()->searchByName('Région');

        self::assertNotEmpty($result);
    }
}
