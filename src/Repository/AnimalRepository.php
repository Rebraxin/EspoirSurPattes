<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function findLastLostFindPets()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
                                SELECT animal
                                FROM App\Entity\Animal animal
                                WHERE animal.status = 'Perdu' OR animal.status = 'Trouvé'
                                ORDER BY animal.createdAt DESC
                                ")
                                ->setMaxResults(10);

        return $query->getResult();
    }
    public function findLastAdoptionPets()
    {
        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery("
                                SELECT animal
                                FROM App\Entity\Animal animal
                                WHERE animal.status = 'Adoption'
                                ORDER BY animal.createdAt DESC
                                ")
                                ->setMaxResults(10);

        return $query->getResult();
    }
    public function findAnimalsByType($id): Query
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
                                SELECT animal,type
                                FROM App\Entity\Animal animal
                                INNER JOIN animal.type type
                                WHERE type.id = $id
                                ORDER BY animal.createdAt DESC
        ");
        return $query;
    }
    public function findPetsByDepartment($departmentId): Query
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
                                SELECT animal,department
                                FROM App\Entity\Animal animal
                                INNER JOIN animal.department department
                                WHERE (animal.status = 'Perdu' OR animal.status ='Trouvé') AND animal.department = $departmentId 
                                ORDER BY animal.createdAt DESC
                                ");
                
        return $query;
    }
    public function findPetsByRegion($regionId): Query
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
                                SELECT animal,department,region
                                FROM App\Entity\Animal animal
                                INNER JOIN animal.department department
                                INNER JOIN department.region region
                                WHERE (animal.status = 'Perdu' OR animal.status ='Trouvé') AND department.region = $regionId 
                                ORDER BY animal.createdAt DESC
                                ");
                
        return $query;
    }
}
