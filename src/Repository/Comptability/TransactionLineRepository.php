<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\BP;
use App\Entity\Comptability\Event;
use App\Entity\Comptability\TransactionLine;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<TransactionLine>
 *
 * @method TransactionLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionLine[]    findAll()
 * @method TransactionLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionLine::class);
    }

    public function save(TransactionLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TransactionLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return TransactionLine[] Returns an array of TransactionLine objects
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

    //    public function findOneBySomeField($value): ?TransactionLine
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findById($value): ?TransactionLine
    {
        return $this->createQueryBuilder('transactionLine')
            ->andWhere('transactionLine.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
             ;
    }
        
    public function findByTransactionAndLabel($id , $label): ?TransactionLine
    {
        return $this->createQueryBuilder('transactionLine')
        ->innerJoin('transactionLine.transaction', 'transaction')
            ->andWhere('transaction.id = :id')
            ->andWhere('transactionLine.label = :label')
            ->setParameter('id', $id)
            ->setParameter('label', $label)
            ->getQuery()
            ->getOneOrNullResult()
             ;
    }



    public function findLastTransactionLine(): ?TransactionLine
    {
        return $this->createQueryBuilder('transactionLine')
            -> orderBy('t.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function totalByBP(BP $bp): array
    {
        return $this->createQueryBuilder('transactionLine')
        ->innerJoin('transactionLine.transaction', 'transaction')
        ->select(" SUM(transactionLine.amount) as total")
        ->Where('transaction.BP = :bp')
        ->setParameter('bp', $bp)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function totalByEvent(Event $event): array
    {
        return $this->createQueryBuilder('transactionLine')
        ->innerJoin('transactionLine.transaction', 'transaction')
        ->select(" SUM(transactionLine.amount) as total")
        ->Where('transaction.event = :event')
        ->setParameter('event', $event)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function totalByFinancementLine( $id): array
    {
        return $this->createQueryBuilder('transactionLine')
        ->innerJoin('transactionLine.transaction', 'transaction')
        ->innerJoin('transaction.financementLine', 'financementLine')
        ->select(" SUM(transactionLine.amount) as total")
        ->Where('financementLine.id = :id')
        ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function totalByTransaction($id): array
    {
        return $this->createQueryBuilder('transactionLine')
        ->innerJoin('transactionLine.transaction', 'transaction')
        ->select(" SUM(transactionLine.amount) as total")
        ->Where('transaction.id = :id')
        ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }



}
