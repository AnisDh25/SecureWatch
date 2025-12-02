<?php

namespace App\Repository;

use App\Entity\Incident;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incident>
 */
class IncidentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incident::class);
    }

    public function save(Incident $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearchQuery(string $query, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('i');
        
        $qb->where($qb->expr()->orX(
            'i.notes LIKE :query',
            'i.status LIKE :query',
            'i.severity LIKE :query'
        ))
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('i.openedAt', 'DESC')
        ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
}
