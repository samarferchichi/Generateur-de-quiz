<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 05/05/19
 * Time: 23:06
 */

namespace App\Controller;


use App\Entity\Page;
use App\Entity\Parametre;
use App\Entity\ParticipantQuiz;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\PageType;
use App\Form\ParametreType;
use App\Form\QuestionType;
use App\Repository\PageRepository;
use App\Repository\ParametreRepository;
use App\Repository\ParticipantQuizRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


/**
 * Common features needed in admin controllers.
 *
 * @internal
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class StatistiqueController extends EasyAdminController
{

    /**
     * @Route("/statistique", name="statistique")
     */
    public function statistique( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository )
    {

        $listquiz = $quizRepository->findAll();
        $participant = $participantQuizRepository->findAll();
        $listparticipantquiz=$participantQuizRepository->findAll();
$tentative=0;
        $userquiz = array();
        $quiz = array();
        $participantquiz = array();

        $test=0;
        $find=0;
        $tro=0;
        $i=0;
        $j=0;



$thisuser=new User();

        foreach ($listquiz as $a)
        {
            if ($a->getUser() == $this->getUser()){

                //array_push($userquiz,$a->getTitre());
                array_push($quiz,$a);

                foreach ($participant as $p){
                    if ($p->getQuiz() == $a){
                        $find=1;
                    }
                }
                if ($find == 0)
                array_push($participantquiz,0);
                else{

                    $find=0;

                    $em = $this->getDoctrine()->getManager();




                    $query = 'select count(*) as num from participant_quiz group by quiz ';


                    $statement = $em->getConnection()->prepare($query);

                    $statement->execute();

                    $result = $statement->fetchAll();

                    array_push($participantquiz,$result[$i]['num']);

                    $i=$i+1;
                }
            }
        }


        return $this->render('quiz/statistique.html.twig',
            [
                'userquiz' =>$quiz,
                'participant' =>$participantquiz,


            ]);
    }



}