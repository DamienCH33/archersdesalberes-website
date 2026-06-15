<?php

namespace App\Repository;

use App\Entity\TeamMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamMember>
 */
class TeamMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamMember::class);
    }

    /**
     * Trouve tous les membres de l'équipe pour affichage
     *
     * @return TeamMember[]
     */
    public function findAllForDisplay(): array
    {
        return $this->createQueryBuilder('tm')
            ->orderBy('tm.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les membres par rôle
     *
     * @return TeamMember[]
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('tm')
            ->where('LOWER(tm.role) LIKE LOWER(:role)')
            ->setParameter('role', '%' . $role . '%')
            ->orderBy('tm.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le président
     */
    public function findPresident(): ?TeamMember
    {
        return $this->createQueryBuilder('tm')
            ->where('LOWER(tm.role) LIKE LOWER(:role)')
            ->setParameter('role', '%président%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre de membres dans l'équipe
     */
    public function countMembers(): int
    {
        return $this->createQueryBuilder('tm')
            ->select('COUNT(tm.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche de membres par nom
     *
     * @return TeamMember[]
     */
    public function searchByName(string $query): array
    {
        return $this->createQueryBuilder('tm')
            ->where('LOWER(tm.firstName) LIKE LOWER(:query)')
            ->orWhere('LOWER(tm.lastName) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('tm.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
