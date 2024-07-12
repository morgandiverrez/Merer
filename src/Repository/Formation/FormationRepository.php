<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function add(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Formation[] Returns an array of Formation objects
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

    //    public function findOneBySomeField($value): ?Formation
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
       return $this->createQueryBuilder('formation')
           ->andWhere('formation.id = :val')
           ->setParameter('val', $value)
          // ->orderBy('formation.id', 'ASC')
           ->setMaxResults(1)
           ->getQuery()
            ->getResult()
       ;
   }

    public function findByName($value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.name LIKE :val')
        ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName( $value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.name LIKE :val')
        ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByOPG($value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.opg LIKE :val')
        ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByPublicCible($value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.public_cible LIKE :val')
        ->setParameter('val', '%'.$value.'%')
        ->getQuery()
            ->getResult();
    }

    public function findAllByPreRequis($value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.pre_requis LIKE :val')
        ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCategorie($value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.categorie LIKE :val')
        ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllDurationInferior( $value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.duration <= :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findAllDurationSuperior( $value): ?array
    {
        return $this->createQueryBuilder('formation')
        ->andWhere('formation.duration >= :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
