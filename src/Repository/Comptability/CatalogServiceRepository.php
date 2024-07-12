<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\CatalogService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CatalogService>
 *
 * @method CatalogService|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatalogService|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatalogService[]    findAll()
 * @method CatalogService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogService::class);
    }

    public function add(CatalogService $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CatalogService $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return CatalogService[] Returns an array of CatalogService objects
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

    //    public function findOneBySomeField($value): ?CatalogService
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByCode($value): ?CatalogService
    {
        return $this->createQueryBuilder('catalogService')
        ->Where('catalogService.code LIKE :val')
        ->setParameter('val',  $value )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByID($value): ?CatalogService
    {
        return $this->createQueryBuilder('service')
        ->andWhere('service.id = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByCode($value): ?array
    {
        return $this->createQueryBuilder('service')
        ->andWhere('service.code LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }
    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('service')
        ->andWhere('service.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }
    public function findAllByAmountTtc($value): ?array
    {
        return $this->createQueryBuilder('service')
        ->andWhere('service.amountTtc = :val')
        ->setParameter('val',  $value )
            ->getQuery()
            ->getResult();
    }

}
