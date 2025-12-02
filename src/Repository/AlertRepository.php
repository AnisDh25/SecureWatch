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
        
        $qb->where($qb->expr()->orX(
            'a.title LIKE :query',
            'a.message LIKE :query',
            'a.source LIKE :query'
        ))
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('a.createdAt', 'DESC')
        ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
}
