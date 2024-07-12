<?php

namespace App\Repository\Comptability;

use App\Entity\Comptability\Event;

use App\Entity\Comptability\Exercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
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

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllByName($value): ?array
    {
        return $this->createQueryBuilder('event')
        ->andWhere('event.name LIKE :val')
        ->setParameter('val', '%' . $value . '%')
            ->orderBy('event.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

     public function findAllByExercice( $value): ?array
    {
        return $this->createQueryBuilder('event')
        ->innerJoin('event.exercice', 'exercice')
        ->andWhere('exercice.annee LIKE :val')
        ->setParameter('val',  $value )
            ->orderBy('event.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllInOrder(): ?array
    {
        return $this->createQueryBuilder('event')
            ->orderBy('event.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    

}
