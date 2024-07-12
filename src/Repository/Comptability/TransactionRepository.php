<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    

    public function findTransactionById($value): ?Transaction
       {
           return $this->createQueryBuilder('transaction')
               ->andWhere('transaction.id = :val')
               ->setParameter('val', $value)
               ->getQuery()
            ->getOneOrNullResult()
           ;
       }


    public function findTransactionByCode($value): ?Transaction
    {
        return $this->createQueryBuilder('transaction')
        ->andWhere('transaction.code = :val')
        ->setParameter('val', $value)
        ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMaxDayTransaction($value)
    {
        return $this->createQueryBuilder('t')
            ->select('t.code')
            ->where('t.code-?1>0')
            ->andWhere('t.code-?1<99')
            ->setParameter(1, $value)
            ->orderBy('t.code', "DESC")
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }

    public function findAllByCode($value): ?array
    {
        return $this->createQueryBuilder('transaction')
        ->andWhere('transaction.code LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('transaction.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCommentaire($value): ?array
    {
        return $this->createQueryBuilder('transaction')
        ->andWhere('transaction.quote LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('transaction.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllComfirm(): ?array
    {
        return $this->createQueryBuilder('transaction')
        ->andWhere('transaction.closure = true')
        ->orderBy('transaction.code', 'DESC')
        ->getQuery()
            ->getResult();
    }

    public function findAllNotComfirm(): ?array
    {
        return $this->createQueryBuilder('transaction')
        ->andWhere('transaction.closure = false')
        ->orderBy('transaction.code', 'DESC')
        ->getQuery()
            ->getResult();
    }

   
}
