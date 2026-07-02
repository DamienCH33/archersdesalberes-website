<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class SettingRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function repository(): SettingRepository
    {
        return self::getContainer()->get(SettingRepository::class);
    }

    public function testUpsertCreatesThenUpdates(): void
    {
        self::bootKernel();

        $repo = $this->repository();

        $repo->upsertSetting('contact_email', 'a@archers.fr');
        self::assertSame('a@archers.fr', $repo->getValueByKey('contact_email'));

        // Deuxième appel : mise à jour, pas de doublon.
        $repo->upsertSetting('contact_email', 'b@archers.fr');
        self::assertSame('b@archers.fr', $repo->getValueByKey('contact_email'));
    }

    public function testFindOneByKey(): void
    {
        self::bootKernel();

        $this->repository()->upsertSetting('phone', '0600000000');

        $setting = $this->repository()->findOneByKey('phone');

        self::assertNotNull($setting);
    }

    public function testGetValueByKeyReturnsDefaultWhenAbsent(): void
    {
        self::bootKernel();

        self::assertSame('fallback', $this->repository()->getValueByKey('inexistant', 'fallback'));
    }

    public function testExists(): void
    {
        self::bootKernel();

        $this->repository()->upsertSetting('site_title', 'Archers');

        self::assertTrue($this->repository()->exists('site_title'));
        self::assertFalse($this->repository()->exists('autre_cle'));
    }

    public function testDeleteByKey(): void
    {
        self::bootKernel();

        $this->repository()->upsertSetting('temp', 'value');
        self::assertTrue($this->repository()->exists('temp'));

        $deleted = $this->repository()->deleteByKey('temp');

        self::assertTrue($deleted);
        self::assertFalse($this->repository()->exists('temp'));
    }

    public function testGetAllAsArray(): void
    {
        self::bootKernel();

        $this->repository()->upsertSetting('k1', 'v1');
        $this->repository()->upsertSetting('k2', 'v2');

        $all = $this->repository()->getAllAsArray();

        self::assertArrayHasKey('k1', $all);
        self::assertArrayHasKey('k2', $all);
        self::assertSame('v1', $all['k1']);
    }
}
