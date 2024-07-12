<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Seance;
use App\Entity\Formation\Evenement;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Seance>
 *
 * @method Seance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seance[]    findAll()
 * @method Seance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function add(Seance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Seance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Seance[] Returns an array of Seance objects
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

    //    public function findOneBySomeField($value): ?Seance
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllOrderByDate(): array
    {
        return $this->createQueryBuilder('seance')
            ->orderBy('seance.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByID($value): array
    {
        return $this->createQueryBuilder('seance')
            ->andWhere('seance.id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllByParcours(Evenement $event, String $value): ?array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.evenement = :event')
            ->andWhere('seance.parcours = :val')
            ->setParameter('event', $event)
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement($value): array
    {
        return $this->createQueryBuilder('seance')
            ->where('seance.visible = true')
            ->andWhere('seance.datetime >= :val')
            ->andWhere('seance.evenement IS NULL ')
            ->setParameter('val', $value)
            ->orderBy('seance.datetime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findAllByYear($datedebut, $datefin): array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.datetime >=:datedebut')
            ->andWhere('seance.datetime <:datefin')
            ->setParameter('datedebut', $datedebut)
            ->setParameter('datefin', $datefin)
            ->orderBy('seance.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByEvenement($value): array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.evenement LIKE :val')
            ->setParameter('val', $value)
            ->orderBy('seance.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('seance')
            ->andWhere('seance.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

 

    public function findAllByDateTimeSuperior($value): array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.datetime >= :val')
            ->setParameter('val', $value)
            ->orderBy('seance.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByDateTimeInferior($value): array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.datetime <= :val')
            ->setParameter('val', $value)
            ->orderBy('seance.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

      public function findByEvenementAndParcourAndDatetime($evenement, $name, $datetime ): array
    {
        return $this->createQueryBuilder('seance')
            ->Where('seance.evenement = :evenement')
            ->AndWhere('seance.name = :name')
            ->AndWhere('seance.datetime LIKE :datetime')
            ->setParameter('evenement', $evenement, )
            ->setParameter('name', $name)
            ->setParameter('datetime', $datetime)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }


   

}
