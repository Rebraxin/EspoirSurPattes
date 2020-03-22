<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Type;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class TypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    public function findWidgetCategories()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
            SELECT type
            FROM App\Entity\Type type
            WHERE type.name = 'Chien' OR type.name = 'Chat' OR type.name = 'Oiseau'
        ");

        return $query->getResult();
    }

}
