<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\FundBox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FundBox>
 *
 * @method FundBox|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundBox|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundBox[]    findAll()
 * @method FundBox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundBox::class);
    }

    public function save(FundBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FundBox[] Returns an array of FundBox objects
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

    //    public function findOneBySomeField($value): ?FundBox
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByID($value): ?array
    {
        return $this->createQueryBuilder('fundBox')
        ->andWhere('fundBox.id = :val')
        ->setParameter('val', $value)
            ->orderBy('fundBox.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllInOrder(): ?array
    {
        return $this->createQueryBuilder('fundBox')
            ->orderBy('fundBox.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function MontantTotale($val)
    {
        return $this->createQueryBuilder('fundBox')
        ->where('fundBox.id = :val')
        ->innerJoin('fundBox.fundTypeJoin', 'typeJoin')
        ->innerJoin('typeJoin.fundType', 'fundType')
        ->select('SUM(typeJoin.quantity * fundType.amount) as total_amount')
        ->setParameter('val', $val)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('fundBox')
        ->andWhere('fundBox.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('fundBox.name', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
