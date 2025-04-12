<?php

namespace App\Reservation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c', 'u')
            ->leftJoin('r.reservedCar', 'c')
            ->leftJoin('r.createdBy', 'u')
            ->getQuery()
            ->getResult();
    }
}
