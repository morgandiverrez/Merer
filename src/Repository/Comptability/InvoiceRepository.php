<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function add(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Invoice[] Returns an array of Invoice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Invoice
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findByID($value): ?Invoice
    {
        return $this->createQueryBuilder('invoice')
        ->andWhere('invoice.id = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCurrentInvoiceImpressionOfCustomer($id): ?Invoice
    {
        return $this->createQueryBuilder('invoice')
            ->innerJoin('invoice.customer', 'customer')
            ->Where('customer.id LIKE :id')
            ->andWhere('invoice.category LIKE :category')
            ->andWhere('invoice.comfirm LIKE :false')
            ->setParameter('id', $id )
            ->setParameter('false', false)
            ->setParameter('category', 'Impression' )
            ->orderBy('invoice.creationDate', 'DESC')
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

    public function findAllByCustomer($value): ?array
    {
        return $this->createQueryBuilder('invoice')
            ->innerJoin('invoice.customer', 'customer')
            ->andWhere('customer.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('invoice.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCode($value): ?array
    {
        return $this->createQueryBuilder('invoice')
        ->andWhere('invoice.code LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('invoice.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByTransaction($value): ?array
    {
        return $this->createQueryBuilder('invoice')
            ->innerJoin('invoice.transaction', 'transaction')
            ->andWhere('transaction.code LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('invoice.code', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllComfirm(): ?array
    {
        return $this->createQueryBuilder('invoice')
        ->andWhere('invoice.comfirm = true')
        ->orderBy('invoice.code', 'DESC')
        ->getQuery()
            ->getResult();
    }

    public function findAllNotComfirm(): ?array
    {
        return $this->createQueryBuilder('invoice')
        ->andWhere('invoice.comfirm = false')
        ->orderBy('invoice.code', 'DESC')
        ->getQuery()
            ->getResult();
    }

 
}
