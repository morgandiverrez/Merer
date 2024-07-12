<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByID($value): ?array
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.id = :val')
            ->setParameter('val', $value)
            // ->orderBy('formation.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

     public function findByMail($value): ?array
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :val')
            ->setParameter('val', $value)
            // ->orderBy('formation.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

     public function findAllIfProfil(): ?array
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.profil != :val')
            ->setParameter('val', null)
            ->orderBy('user.id', 'ASC')
            ->getQuery()
            ->getResult();
            
    }


    public function findAllUserWithProfilAndFormateurice()
    {

         return $this->createQueryBuilder('u')
                    ->orderBy('u.email', 'ASC')
                    ->andWhere('u.roles LIKE :val0 OR  u.roles LIKE :val1')
                    ->setParameter('val0', '%'.'ROLE_FORMATEURICE'.'%')
                    ->setParameter('val1', '%' . 'ROLE_BF' . '%')
                    ->getQuery()
                     ->getResult();
    }
    
    
    

}
