<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\FundTypeFundBox;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<FundTypeFundBox>
 *
 * @method FundTypeFundBox|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundTypeFundBox|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundTypeFundBox[]    findAll()
 * @method FundTypeFundBox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundTypeFundBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundTypeFundBox::class);
    }

    public function save(FundTypeFundBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundTypeFundBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FundTypeFundBox[] Returns an array of FundTypeFundBox objects
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

    //    public function findOneBySomeField($value): ?FundTypeFundBox
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBy2ID($fundTypeID, $fundBoxID ): array
    {
        return $this->createQueryBuilder('fundTypeFundBox')
        ->Where('fundTypeFundBox.fundBox = :fundBoxID')
        ->setParameter('fundBoxID', $fundBoxID)
            ->andWhere('fundTypeFundBox.fundType = :fundTypeID')
            ->setParameter('fundTypeID', $fundTypeID)
            ->getQuery()
            ->getResult();
    }

   
}
