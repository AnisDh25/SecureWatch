<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asset>
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    public function save(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearchQuery(string $query, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('a');
        
        $qb->where($qb->expr()->orX(
            'a.name LIKE :query',
            'a.type LIKE :query',
            'a.ipAddress LIKE :query',
            'a.description LIKE :query'
        ))
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('a.createdAt', 'DESC')
        ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
}
