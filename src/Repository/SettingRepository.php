<?php
// src/Repository/SettingRepository.php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    /**
     * Trouve un paramètre par sa clé
     */
    public function findOneByKey(string $key): ?Setting
    {
        return $this->createQueryBuilder('s')
            ->where('s.settingKey = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère la valeur d'un paramètre par sa clé
     */
    public function getValueByKey(string $key, ?string $default = null): ?string
    {
        $setting = $this->findOneByKey($key);
        return $setting ? $setting->getSettingValue() : $default;
    }

    /**
     * Met à jour ou crée un paramètre
     */
    public function upsertSetting(string $key, string $value, ?string $description = null): Setting
    {
        $setting = $this->findOneByKey($key);

        if (!$setting) {
            $setting = new Setting();
            $setting->setSettingKey($key);
        }

        $setting->setSettingValue($value);
        if ($description !== null) {
            $setting->setDescription($description);
        }
        $setting->setUpdatedAt(new \DateTimeImmutable());

        $this->getEntityManager()->persist($setting);
        $this->getEntityManager()->flush();

        return $setting;
    }

    /**
     * Récupère tous les paramètres sous forme de tableau clé => valeur
     *
     * @return array<string, string>
     */
    public function getAllAsArray(): array
    {
        $settings = $this->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->getSettingKey()] = $setting->getSettingValue();
        }

        return $result;
    }

    /**
     * Trouve tous les paramètres triés par clé
     *
     * @return Setting[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.settingKey', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime un paramètre par sa clé
     */
    public function deleteByKey(string $key): bool
    {
        $setting = $this->findOneByKey($key);

        if (!$setting) {
            return false;
        }

        $this->getEntityManager()->remove($setting);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Vérifie si un paramètre existe
     */
    public function exists(string $key): bool
    {
        return $this->findOneByKey($key) !== null;
    }
}
