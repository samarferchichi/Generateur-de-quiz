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

    public function getParticipants ($user){
        $qb = $this->createQueryBuilder('p')
            ->select('p AS Part, count(q) AS NbParticipant, q.titre AS titleQuiz, q.id AS idQuiz')
            ->join('p.quiz', 'q')
            ->join('q.user', 'u')
            ->where('u.id = ?1')
            ->setParameter(1, $user)
            ->groupBy('q')
            ->orderBy('NbParticipant', 'desc')
            ->setMaxResults(10)
            ->getQuery();

        return $qb->getArrayResult();
    }

    public function getTotalParticipants ($user){
        $qb = $this->createQueryBuilder('p')
            ->select('count(q) AS NbParticipant')
            ->join('p.quiz', 'q')
            ->join('q.user', 'u')
            ->where('u.id = ?1')
            ->setParameter(1, $user)
            ->getQuery();

        return $qb->getArrayResult();
    }

    public function getMaxTentatives ($user){
        $qb = $this->createQueryBuilder('p')
                   ->select('p AS Part, SUM(p.tentative) AS NbTentatives, q.titre AS titleQuiz, q.id AS idQuiz')
                   ->join('p.quiz', 'q')
                   ->join('q.user', 'u')
                   ->where('u.id = ?1')
                   ->setParameter(1, $user)
                   ->groupBy('q')
                   ->orderBy('NbTentatives', 'desc')
                   ->setMaxResults(10)
                   ->getQuery();

        return $qb->getResult();
    }


    public function getAllParticipants ($user){
        $qb = $this->createQueryBuilder('p')
            ->select('p AS Part, count(q) AS NbParticipant, q.titre AS titleQuiz, q.id AS idQuiz')
            ->join('p.quiz', 'q')
            ->join('q.user', 'u')
            ->where('u.id = ?1')
            ->setParameter(1, $user)
            ->groupBy('q')
            ->orderBy('NbParticipant', 'desc')
            ->getQuery();

        return $qb->getArrayResult();
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
