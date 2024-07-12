<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Badge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Badge>
 *
 * @method Badge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badge[]    findAll()
 * @method Badge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    public function add(Badge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Badge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Badge[] Returns an array of Badge objects
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

    //    public function findOneBySomeField($value): ?Badge
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByID($value): ?array
    {
        return $this->createQueryBuilder('badge')
        ->andWhere('badge.id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('badge')
        ->andWhere('badge.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
        ->getQuery()
            ->getResult();
    }

    public function findAllByDescription($value): ?array
    {
        return $this->createQueryBuilder('badge')
        ->andWhere('badge.description LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCategorie($value): ?array
    {
        return $this->createQueryBuilder('badge')
        ->andWhere('badge.categorie LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }


}
