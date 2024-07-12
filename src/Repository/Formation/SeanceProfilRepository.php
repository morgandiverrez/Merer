<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Seance;
use App\Entity\Formation\Evenement;
use App\Entity\Formation\SeanceProfil;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SeanceProfil>
 *
 * @method SeanceProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeanceProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeanceProfil[]    findAll()
 * @method SeanceProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeanceProfil::class);
    }

    public function add(SeanceProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SeanceProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return SeanceProfil[] Returns an array of SeanceProfil objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SeanceProfil
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findAllBySeance(Seance $value): array
   {
       return $this->createQueryBuilder('inscrit')
            ->innerJoin('inscrit.profil' , 'profil')
            ->select("profil.id")
            ->Where('inscrit.seance = :val ')
           ->setParameter('val', $value)
           ->getQuery()
           ->getResult()
       ;
   }

    public function CountBySeance(Seance $value): array
    {
        return $this->createQueryBuilder('inscrit')
        ->select("COUNT('inscrit')")
        ->Where('inscrit.seance = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function CountByEvenement(Evenement $evenement): array
    {
        return $this->createQueryBuilder('inscrit')
        ->select("COUNT('inscrit')")
        ->innerJoin('inscrit.seance', 'seance')
        ->Where('seance.evenement = :evenement')
        ->setParameter('evenement', $evenement)
            ->getQuery()
            ->getResult();
    }

    public function findBy2ID($seanceID, $profilID): array
    {
        return $this->createQueryBuilder('seanceProfil')
        ->Where('seanceProfil.seance = :seanceID')
        ->setParameter('seanceID', $seanceID)
        ->andWhere('seanceProfil.profil = :profilID')
        ->setParameter('profilID', $profilID)
        ->getQuery()
        ->getResult();
    }

   
}
