<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findBySearchQuery(string $query, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('a');
        
        $qb->leftJoin('a.alertRule', 'ar')
           ->leftJoin('a.event', 'e')
           ->where($qb->expr()->orX(
            'a.severity LIKE :query',
            'a.status LIKE :query',
            'ar.name LIKE :query',
            'ar.condition LIKE :query',
            'e.source LIKE :query',
            'e.data LIKE :query'
        ))
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('a.createdAt', 'DESC')
        ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
}
