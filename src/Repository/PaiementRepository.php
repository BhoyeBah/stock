<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function getMontantPaye(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.montant), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getMontantPayeParMois(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT MONTH(created_at) AS mois, COALESCE(SUM(montant),0) AS total
        FROM paiement
        GROUP BY mois
        ORDER BY mois ASC
    ';

        // executeQuery() retourne un Result
        $results = $conn->executeQuery($sql)->fetchAllAssociative();

        $data = [];
        foreach ($results as $row) {
            $data[(int)$row['mois']] = (int)$row['total'];
        }

        // Remplir les mois manquants
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($data[$i])) $data[$i] = 0;
        }

        ksort($data);

        $labels = ['Janv', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $final = [];
        foreach ($data as $mois => $montant) {
            $final[$labels[$mois - 1]] = $montant;
        }

        return $final;
    }


    //    /**
    //     * @return Paiement[] Returns an array of Paiement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Paiement
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
