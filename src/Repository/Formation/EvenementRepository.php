<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Seance;
use App\Entity\Formation\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function add(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Evenement[] Returns an array of Evenement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evenement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByID($value): array
    {
        return $this->createQueryBuilder('evenement')
            ->andWhere('evenement.id = :val')
            ->setParameter('val', $value)
            ->orderBy('evenement.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

      public function findByName($value): array
    {
        return $this->createQueryBuilder('evenement')
            ->andWhere('evenement.name = :val')
            ->setParameter('val', $value)
            ->orderBy('evenement.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByDate(): array
   {
       return $this->createQueryBuilder('evenement')
           ->orderBy('evenement.dateDebut', 'DESC')
           ->getQuery()
            ->getResult();
       ;
   }


    public function findAllSuperiorByDatetimeAndVisible($value): array
    {
        return $this->createQueryBuilder('evenement')
        ->where('evenement.visible = true')
        ->andWhere('evenement.dateFin >= :val')
        ->setParameter('val', $value)
            ->orderBy('evenement.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('evenement')
        ->andWhere('evenement.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('evenement.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByDescription($value): ?array
    {
        return $this->createQueryBuilder('evenement')
        ->andWhere('evenement.description LIKE :val')
        ->setParameter('val', $value . '%')
            ->orderBy('evenement.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }


}
