<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Impression;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Impression>
 *
 * @method Impression|null find($id, $lockMode = null, $lockVersion = null)
 * @method Impression|null findOneBy(array $criteria, array $orderBy = null)
 * @method Impression[]    findAll()
 * @method Impression[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImpressionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Impression::class);
    }

    public function add(Impression $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Impression $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Impression[] Returns an array of Impression objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Impression
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllByCustomer($value): ?array
    {
        return $this->createQueryBuilder('impression')
            ->innerJoin('impression.customer', 'customer')
            ->Where('customer.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('impression.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByFormat($value): ?array
    {
        return $this->createQueryBuilder('impression')
        ->Where('impression.format LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('impression.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllIfRV($value): ?array
    {
        return $this->createQueryBuilder('impression')
            ->Where('impression.format != plastification')
        ->Where('impression.rectoVerso = :val')
        ->setParameter('val', $value )
            ->orderBy('impression.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAllIfCouleur($value): ?array
    {
        return $this->createQueryBuilder('impression')
            ->Where('impression.format != plastification')
        ->Where('impression.couleur = :val')
        ->setParameter('val',  $value )
            ->orderBy('impression.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findByID($value): ?Impression
    {
        return $this->createQueryBuilder('impression')
        ->andWhere('impression.id = :val')
        ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllinOrder(): ?array
    {
        return $this->createQueryBuilder('impression')
            ->orderBy('impression.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
