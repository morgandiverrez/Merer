<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Financement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Financement>
 *
 * @method Financement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Financement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Financement[]    findAll()
 * @method Financement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinancementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Financement::class);
    }

    public function save(Financement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Financement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Financement[] Returns an array of Financement objects
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

    //    public function findOneBySomeField($value): ?Financement
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
        return $this->createQueryBuilder('financement')
        ->orderBy('financement.name', 'DESC')
        ->getQuery()
            ->getResult();
    }

    public function findFinancementById($value): ?Financement
       {
           return $this->createQueryBuilder('f')
               ->andWhere('f.id = :val')
               ->setParameter('val', $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('financement')
        ->andWhere('financement.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('financement.dateVersement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByFinanceur($value): ?array
    {
        return $this->createQueryBuilder('financement')
        ->andWhere('financement.financeur LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('financement.dateVersement', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
