<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\ORM\Query;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Query
     */
    public function findAllArticles(): Query
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager ->createQuery("
            SELECT article  
            FROM App\Entity\Article article 
            ORDER BY article.createdAt DESC 
            ");

        return $query;
    }

    public function findLastArticles()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager ->createQuery("
            SELECT article  
            FROM App\Entity\Article article 
            ORDER BY article.createdAt DESC 
            ")
            ->setMaxResults(5);

        return $query->getResult();
    }

    /**
     * @return Query
     */
    public function findArticlesByCategory($id): Query
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
            SELECT article,category
            FROM App\Entity\Article article
            INNER JOIN article.categories category
            WHERE category.id = $id
            ORDER BY article.createdAt DESC
        ");
        return $query;
    }

}
