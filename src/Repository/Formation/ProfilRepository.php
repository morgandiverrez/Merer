<?php

namespace App\Repository\Formation;

use App\Entity\Formation\Profil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profil>
 *
 * @method Profil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profil[]    findAll()
 * @method Profil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profil::class);
    }

    public function add(Profil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Profil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Profil[] Returns an array of Profil objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Profil
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByID($value): ?array
    {
        return $this->createQueryBuilder('profil')
            ->andWhere('profil.id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findByUser($value): ?Profil
    {
        return $this->createQueryBuilder('profil')
        ->innerJoin('profil.user', 'user')
            ->andWhere('user.id = :val')
             ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByName($name, $lastName): ?array
    {
        return $this->createQueryBuilder('profil')
        ->where('profil.name LIKE :name')
        ->andWhere('profil.last_name LIKE :last_name')
        ->setParameter('name', $name)
        ->setParameter('last_name', $lastName)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('profil')
        ->andWhere('profil.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
        ->getQuery()
            ->getResult();
    }

    public function findAllByLastName($value): ?array
    {
        return $this->createQueryBuilder('profil')
        ->andWhere('profil.last_name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
        ->getQuery()
            ->getResult();
    }

    public function findAllDobSuperior($value): ?array
    {
        return $this->createQueryBuilder('profil')
        ->andWhere('profil.date_of_birth >= :val')
        ->setParameter('val',  $value )
            ->getQuery()
            ->getResult();
    }

    public function findAllDobInferior($value): ?array
    {
        return $this->createQueryBuilder('profil')
        ->andWhere('profil.date_of_birth <= :val')
        ->setParameter('val',  $value )
            ->getQuery()
            ->getResult();
    }

    

    public function findAllByInscription($value): ?array
    {
        return $this->createQueryBuilder('profil')
        ->andWhere('profil.seanceProfil LIKE :val')
        ->setParameter('val',  $value )
        ->getQuery()
            ->getResult();
    }
    public function findAllFormateurice(): ?array
    {
        return $this->createQueryBuilder('profil')
                ->innerJoin('profil.user', 'u')
                 ->orderBy('u.email', 'ASC')
                    ->andWhere('u.roles LIKE :val0 OR  u.roles LIKE :val1')
                    ->setParameter('val0', '%'.'ROLE_FORMATEURICE'.'%')
                    ->setParameter('val1', '%' . 'ROLE_BF' . '%')
                    ->getQuery()
                     ->getResult();
    }

   
}
