<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * Renvoie tous les horaires groupés par catégorie, triés par displayOrder.
     *
     * @return array<string, Schedule[]>
     */
    public function findAllGroupedByCategory(): array
    {
        $schedules = $this->findBy([], ['displayOrder' => 'ASC']);

        $grouped = [];
        foreach ($schedules as $schedule) {
            $grouped[$schedule->getCategory()][] = $schedule;
        }

        return $grouped;
    }
}
