<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findBySearchQuery(string $query, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('e');
        
        $qb->where($qb->expr()->orX(
            'e.title LIKE :query',
            'e.description LIKE :query',
            'e.source LIKE :query'
        ))
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('e.timestamp', 'DESC')
        ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
}
