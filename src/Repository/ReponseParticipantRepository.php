<?php

namespace App\Repository;

use App\Entity\ReponseParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponseParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseParticipant[]    findAll()
 * @method ReponseParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseParticipantRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponseParticipant::class);
    }


    public function getTentativeMax($quiz)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('MAX(p.ordre) AS maxOrdre')
            ->innerJoin('p.quiz', 'q')
            ->where('q.id = ?1')
            ->setParameter(1, $quiz)
            ->getQuery();

        return $qb->getResult();
    }



    // /**
    //  * @return ReponseParticipant[] Returns an array of ReponseParticipant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReponseParticipant
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
