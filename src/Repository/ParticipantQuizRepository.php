<?php

namespace App\Repository;

use App\Entity\ParticipantQuiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ParticipantQuiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipantQuiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipantQuiz[]    findAll()
 * @method ParticipantQuiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantQuizRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParticipantQuiz::class);
    }

    // /**
    //  * @return ParticipantQuiz[] Returns an array of ParticipantQuiz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ParticipantQuiz
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
