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

    public function getReponseForQuestion($question, $participant, $tentative){

        $qb = $this->createQueryBuilder('r')
            ->join('r.id_question', 'q')
            ->join('r.resultat', 'res')
            ->join('res.participantquiz', 'partQuiz')
            ->join('partQuiz.participant', 'part')
            ->where('q.id = ?1')
            ->andWhere('res.tentative = ?2')
            ->andWhere('part.id = ?3')
            ->setParameter(1, $question)
            ->setParameter(2, $tentative)
            ->setParameter(3, $participant)
            ->getQuery();

        return $qb->getArrayResult();
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
