<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\BP;
use App\Entity\Comptability\Exercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BP>
 *
 * @method BP|null find($id, $lockMode = null, $lockVersion = null)
 * @method BP|null findOneBy(array $criteria, array $orderBy = null)
 * @method BP[]    findAll()
 * @method BP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BP::class);
    }

    public function save(BP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return BP[] Returns an array of BP objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BP
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('bp')
        ->andWhere('bp.designation LIKE :val')
        ->setParameter('val', '%' . $value . '%')
        ->orderBy('bp.designation', 'DESC')
        ->getQuery()
            ->getResult();
    }

    public function findAllByExercice(Exercice $exercice): ?array
    {
        return $this->createQueryBuilder('bp')
        ->where('bp.exercice LIKE :val')
        ->setParameter('val',  $exercice )
        ->orderBy('bp.designation', 'DESC')
        ->getQuery()
            ->getResult();
    }

    public function findAllByExerciceProduit( $exercice): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount >= 0')
            ->setParameter('val',  $exercice)
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAllByExerciceCharge( $exercice): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount < 0')
            ->setParameter('val',  $exercice)
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAllByDesignationPlus($exercice, $value): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount >= 0')
            ->setParameter('val',  $exercice)
            ->andWhere('bp.designation LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByDesignationMoins($exercice, $value): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount < 0')
            ->setParameter('val',  $exercice)
            ->andWhere('bp.designation LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByCategoriePlus($exercice, $value): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount >= 0')
            ->setParameter('val',  $exercice)
            ->andWhere('bp.categorie LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByCategorieMoins($exercice, $value): ?array
    {
        return $this->createQueryBuilder('bp')
            ->innerJoin('bp.exercice', 'exercice')
            ->where('exercice.annee = :val')
            ->andWhere('bp.expectedAmount < 0')
            ->setParameter('val',  $exercice)
            ->andWhere('bp.categorie LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('bp.designation', 'DESC')
            ->getQuery()
            ->getResult();
    }

        
    
}
