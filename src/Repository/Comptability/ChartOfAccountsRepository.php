<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\ChartOfAccounts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChartOfAccounts>
 *
 * @method ChartOfAccounts|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChartOfAccounts|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChartOfAccounts[]    findAll()
 * @method ChartOfAccounts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartOfAccountsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartOfAccounts::class);
    }

    public function save(ChartOfAccounts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChartOfAccounts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ChartOfAccounts[] Returns an array of ChartOfAccounts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ChartOfAccounts
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('chartOfAccounts')
            ->andWhere('chartOfAccounts.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('chartOfAccounts.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCode($value): ?array
    {
        return $this->createQueryBuilder('chartOfAccounts')
            ->andWhere('chartOfAccounts.code = :val')
            ->setParameter('val',  $value )
            -> orderBy('chartOfAccounts.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCategorie($value): ?array
    {
        return $this->createQueryBuilder('chartOfAccounts')
        ->andWhere('chartOfAccounts.code >= :val1')
            ->andWhere('chartOfAccounts.code <:val2')
        ->setParameter('val1',  $value)
        ->setParameter('val2',  $value + 10000)
            ->orderBy('chartOfAccounts.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function totalByChartOfAccounts($id ): array
    {
        return $this->createQueryBuilder('ChartOfAccounts')
        ->innerJoin('ChartOfAccounts.transactionLines', 'transactionLines')
         ->innerJoin('transactionLines.transaction', 'transaction')
         ->innerJoin('transaction.exercice', 'exercice')
        ->select(" SUM(transactionLines.amount) as total")
        ->Where('ChartOfAccounts.id = :id')
         
        ->setParameter('id', $id)
             ->getQuery()
            ->getOneOrNullResult();
    }


    // public function findAllByMovable($value): ?array
    // {
    //     return $this->createQueryBuilder('chartOfAccounts')
    //         ->andWhere('chartOfAccounts.movable = :val')
    //         ->setParameter('val',  $value )
    //         -> orderBy('chartOfAccounts.code', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findAllInOrder(): ?array
    {
        return $this->createQueryBuilder('chartOfAccounts')
        ->orderBy('chartOfAccounts.code', 'DESC')
        ->getQuery()
            ->getResult();
    }


      public function findMaxChartOfAccount($value)
    {
        return $this->createQueryBuilder('account')
            ->select('account.code')
            ->where('account.code-?1>0')
            ->andWhere('account.code-?1<99')
            ->setParameter(1, $value)
            ->orderBy('account.code', "DESC")
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
      
    }

}
