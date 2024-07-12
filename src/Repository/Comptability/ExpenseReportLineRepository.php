<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\ExpenseReportLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseReportLine>
 *
 * @method ExpenseReportLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpenseReportLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpenseReportLine[]    findAll()
 * @method ExpenseReportLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseReportLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseReportLine::class);
    }

    public function save(ExpenseReportLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExpenseReportLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ExpenseReportLine[] Returns an array of ExpenseReportLine objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ExpenseReportLine
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function totalByExpenseReport( $id): array
    {
        return $this->createQueryBuilder('expenseReportLine')
            ->innerJoin('expenseReportLine.expenseReport', 'expenseReport')
            ->select(" SUM(expenseReportLine.amount) as total")
            ->Where('expenseReport.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
