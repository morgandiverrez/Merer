<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Federation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Federation>
 *
 * @method Federation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Federation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Federation[]    findAll()
 * @method Federation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FederationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Federation::class);
    }

    public function save(Federation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Federation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Federation[] Returns an array of Federation objects
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

    //    public function findOneBySomeField($value): ?Federation
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBySocialReason($value) : array
    {
        return $this->createQueryBuilder('federation')
            ->where('federation.socialReason LIKE :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
