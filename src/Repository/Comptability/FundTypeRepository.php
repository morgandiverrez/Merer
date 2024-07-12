<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\FundType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FundType>
 *
 * @method FundType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundType[]    findAll()
 * @method FundType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundType::class);
    }

    public function save(FundType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FundType[] Returns an array of FundType objects
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

    //    public function findOneBySomeField($value): ?FundType
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllInOrder(): ?array
    {
        return $this->createQueryBuilder('fundType')
        ->orderBy('fundType.amount', 'ASC')
        ->getQuery()
            ->getResult();
    }
}
