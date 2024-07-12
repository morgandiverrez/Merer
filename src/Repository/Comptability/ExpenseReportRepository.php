<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\ExpenseReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseReport>
 *
 * @method ExpenseReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpenseReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpenseReport[]    findAll()
 * @method ExpenseReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseReport::class);
    }

    public function save(ExpenseReport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExpenseReport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ExpenseReport[] Returns an array of ExpenseReport objects
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

    //    public function findOneBySomeField($value): ?ExpenseReport
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findExpenseReportById($value): ?ExpenseReport
    {
        return $this->createQueryBuilder('expenseReport')
        ->andWhere('expenseReport.id = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findExpenseReportByCode($value): ?ExpenseReport
    {
        return $this->createQueryBuilder('expenseReport')
        ->andWhere('expenseReport.code = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMaxDayExpenseReport($value)
    {
        return $this->createQueryBuilder('expenseReport')
            ->select('expenseReport.code')
            ->where('expenseReport.code-?1>0')
            ->andWhere('expenseReport.code-?1<99')
            ->setParameter(1, $value)
            ->orderBy('expenseReport.code', "DESC")
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }

    public function findAllByDemandeur($value): ?array
    {
        return $this->createQueryBuilder('expenseReport')
            ->innerJoin('expenseReport.supplier', 'supplier')
        ->andWhere('supplier.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('expenseReport.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllComfirm(): ?array
    {
        return $this->createQueryBuilder('expenseReport')
            ->andWhere('expenseReport.comfirm = true')
            ->orderBy('expenseReport.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllNotComfirm(): ?array
    {
        return $this->createQueryBuilder('expenseReport')
            ->andWhere('expenseReport.comfirm = false')
            ->orderBy('expenseReport.date', 'DESC')
            ->getQuery()
            ->getResult();
    }


}
