<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Retour;
use App\Entity\Formation\Seance;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Retour>
 *
 * @method Retour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retour[]    findAll()
 * @method Retour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retour::class);
    }

    public function add(Retour $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Retour $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Retour[] Returns an array of Retour objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Retour
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllBySeance(Seance $value): array
    {
        return $this->createQueryBuilder('retour')
        ->innerJoin('retour.profil', 'profil')
        ->select("profil.id")
        ->Where('retour.seance = :val ')
        ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    
    public function findBySeanceID($value): ?Retour
       {
           return $this->createQueryBuilder('retour')
               ->andWhere('retour.seance = :val')
               ->setParameter('val', $value)
                ->orderBy('retour.id', 'ASC')
                ->setMaxResults(150)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

    public function findBy2ID($seanceID, $profilID): array
    {
        return $this->createQueryBuilder('retour')
        ->innerJoin("retour.seance", "seance")
        ->Where('seance.id = :seanceID')
        ->setParameter('seanceID', $seanceID)
         ->innerJoin("retour.profil", "profil")
        ->andWhere('profil.id = :profilID')
        ->setParameter('profilID', $profilID)
        ->getQuery()
        ->getResult();
    }

 
}
