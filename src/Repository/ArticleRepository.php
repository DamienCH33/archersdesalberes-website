<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Trouve tous les articles publiés, triés par date de publication décroissante.
     *
     * @return Article[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les articles publiés par catégorie.
     *
     * @return Article[]
     */
    public function findPublishedByCategory(string $category, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->andWhere('a.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->orderBy('a.publishedAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les X derniers articles publiés.
     *
     * @return Article[]
     */
    public function findLatestPublished(int $limit = 6): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('a.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve un article publié par son slug.
     */
    public function findOnePublishedBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->where('a.slug = :slug')
            ->andWhere('a.isPublished = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve tous les albums photos publiés.
     *
     * @return Article[]
     */
    public function findPublishedAlbums(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->andWhere('a.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', 'photos')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche d'articles par titre.
     *
     * @return Article[]
     */
    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = :published')
            ->andWhere('LOWER(a.title) LIKE LOWER(:query)')
            ->setParameter('published', true)
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les articles publiés.
     */
    public function countPublished(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.isPublished = :published')
            ->setParameter('published', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte les articles par catégorie.
     */
    public function countByCategory(string $category): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.isPublished = :published')
            ->andWhere('a.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
