<?php

namespace App\Repository;

use App\Entity\ClubStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClubStat>
 */
class ClubStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubStat::class);
    }

    /**
     * Trouve toutes les statistiques pour affichage homepage.
     *
     * @return ClubStat[]
     */
    public function findAllForDisplay(): array
    {
        return $this->createQueryBuilder('cs')
            ->orderBy('cs.statKey', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une statistique par sa clé.
     */
    public function findOneByKey(string $key): ?ClubStat
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.statKey = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère la valeur d'une statistique par sa clé.
     */
    public function getValueByKey(string $key): ?int
    {
        $stat = $this->findOneByKey($key);

        return $stat instanceof ClubStat ? $stat->getStatValue() : null;
    }

    /**
     * Met à jour ou crée une statistique.
     */
    public function upsertStat(string $key, int $value, string $label): ClubStat
    {
        $stat = $this->findOneByKey($key);

        if (!$stat instanceof ClubStat) {
            $stat = new ClubStat();
            $stat->setStatKey($key);
            $stat->setLabel($label);
        }

        $stat->setStatValue($value);
        $stat->setUpdatedAt(new \DateTimeImmutable());

        $this->getEntityManager()->persist($stat);
        $this->getEntityManager()->flush();

        return $stat;
    }

    /**
     * Récupère toutes les stats sous forme de tableau clé => valeur.
     *
     * @return array<string, int>
     */
    public function getAllAsArray(): array
    {
        $stats = $this->findAll();
        $result = [];

        foreach ($stats as $stat) {
            $result[$stat->getStatKey()] = $stat->getStatValue();
        }

        return $result;
    }
}
