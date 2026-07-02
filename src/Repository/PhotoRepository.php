<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Trouve toutes les photos d'un article, triées par ordre d'affichage.
     *
     * @return Photo[]
     */
    public function findByArticle(Article $article): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.article = :article')
            ->setParameter('article', $article)
            ->orderBy('p.displayOrder', 'ASC')
            ->addOrderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les X premières photos d'un article (pour preview).
     *
     * @return Photo[]
     */
    public function findPreviewPhotos(Article $article, int $limit = 3): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.article = :article')
            ->setParameter('article', $article)
            ->orderBy('p.displayOrder', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les photos d'un article.
     */
    public function countByArticle(Article $article): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve le prochain ordre d'affichage pour un article.
     */
    public function getNextDisplayOrder(Article $article): int
    {
        $max = $this->createQueryBuilder('p')
            ->select('MAX(p.displayOrder)')
            ->where('p.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $max + 1;
    }

    /**
     * Supprime toutes les photos d'un article.
     */
    public function deleteByArticle(Article $article): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->where('p.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->execute();
    }
}
