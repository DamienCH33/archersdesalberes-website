<?php

namespace App\Repository;

use App\Entity\ClubHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClubHistory>
 */
class ClubHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubHistory::class);
    }

    public function getSingle(): ?ClubHistory
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }
}
