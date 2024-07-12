<?php

namespace App\Repository\Formation;

use App\Entity\Formation\EquipeElu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipeElu>
 *
 * @method EquipeElu|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipeElu|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipeElu[]    findAll()
 * @method EquipeElu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeEluRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipeElu::class);
    }

    public function add(EquipeElu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EquipeElu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return EquipeElu[] Returns an array of EquipeElu objects
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

    //    public function findOneBySomeField($value): ?EquipeElu
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByID($value): ?array
    {
        return $this->createQueryBuilder('EquipeElu')
            ->andWhere('EquipeElu.id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findByName($value): ?array
    {
        return $this->createQueryBuilder('equipeElu')
            ->where('equipeElu.name LIKE :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('equipeElu')
        ->andWhere('equipeElu.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByCategorie($value): ?array
    {
        return $this->createQueryBuilder('equipeElu')
        ->andWhere('equipeElu.categorie LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByEtablissement($value): ?array
    {
        return $this->createQueryBuilder('equipeElu')
        ->andWhere('equipeElu.etablissement LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllByFedeFi($value): ?array
    {
        return $this->createQueryBuilder('equipeElu')
        ->andWhere('equipeElu.Fede_filliere LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }


}
