<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\CatalogDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CatalogDiscount>
 *
 * @method CatalogDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatalogDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatalogDiscount[]    findAll()
 * @method CatalogDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogDiscount::class);
    }

    public function add(CatalogDiscount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CatalogDiscount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return CatalogDiscount[] Returns an array of CatalogDiscount objects
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

    //    public function findOneBySomeField($value): ?CatalogDiscount
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByID($value): ?CatalogDiscount
    {
        return $this->createQueryBuilder('discount')
        ->andWhere('discount.id = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByCode($value): ?array
    {
        return $this->createQueryBuilder('discount')
        ->andWhere('discount.code LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByDescription($value): ?array
    {
        return $this->createQueryBuilder('discount')
        ->andWhere('discount.description LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }
    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('discount')
        ->andWhere('discount.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }
}
