<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function generateOrderNumber(): string
    {
        $qb = $this->createQueryBuilder('o')
            ->select('MAX(o.numero)')
            ->getQuery();

        $lastNumber = $qb->getSingleScalarResult();

        // On suppose que le numéro est stocké en chaîne numérique
        $lastNumber = $lastNumber ? intval($lastNumber) : 0;

        $nextNumber = $lastNumber + 1;

        // Format à 6 chiffres avec des zéros en tête
        return str_pad((string)$nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getMontantTotal(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COALESCE(SUM(o.montantTotal), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMontantPaye(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COALESCE(SUM(o.montantPaye), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMontantNonPaye(): int
    {
        return $this->getMontantTotal() - $this->getMontantPaye();
    }

    public function countPaid(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.montantPaye = o.montantTotal')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUnpaid(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.montantPaye < o.montantTotal')
            ->getQuery()
            ->getSingleScalarResult();
    }
    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
