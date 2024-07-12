<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\ChequeBox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChequeBox>
 *
 * @method ChequeBox|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChequeBox|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChequeBox[]    findAll()
 * @method ChequeBox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChequeBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChequeBox::class);
    }

    public function save(ChequeBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChequeBox $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ChequeBox[] Returns an array of ChequeBox objects
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

    //    public function findOneBySomeField($value): ?ChequeBox
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function MontantTotale($val)
    {
        return $this->createQueryBuilder('chequeBox')
        ->where('chequeBox.id = :val')
        ->innerJoin('chequeBox.cheques','cheques')
        ->select('SUM(cheques.amount) as total_amount')
        ->setParameter('val',$val)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('chequeBox')
        ->andWhere('chequeBox.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }
}
