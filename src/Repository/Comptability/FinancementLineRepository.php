<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\FinancementLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FinancementLine>
 *
 * @method FinancementLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method FinancementLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method FinancementLine[]    findAll()
 * @method FinancementLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinancementLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinancementLine::class);
    }

    public function save(FinancementLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FinancementLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FinancementLine[] Returns an array of FinancementLine objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FinancementLine
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findFinancementLineById($value): ?FinancementLine
    {
        return $this->createQueryBuilder('financementLine')
            ->andWhere('financementLine.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
   

}
