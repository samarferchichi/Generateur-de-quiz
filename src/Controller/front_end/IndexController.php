<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 02/04/19
 * Time: 11:11
 */

namespace App\Controller\front_end;
use App\Entity\ParticipantQuiz;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\ParticipantQuizRepository;
use App\Repository\QuizRepository;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use http\Env\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/index")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET" , "POST"})
     */
    public function show( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository)
    {
        $listquiz= $quizRepository->findAll();
        $listparticipantquiz= $participantQuizRepository->findAll();




        return $this->render('front_end/index.html.twig',[
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz
        ]);

    }


    /**
     * @Route("/saveparticipant/", name="saveparticipant", methods={"GET" , "POST"})
     */
    public function saveparticipant()
    {
        $entityManager = $this->getDoctrine()->getManager();


        return $this->render('front_end/test.html.twig');

    }






    /**
     * @Route("/quizpublic/{quiz}", name="quizpublic", methods={"GET" , "POST"})
     */
    public function quizpublic( Quiz $quiz, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository , \Symfony\Component\HttpFoundation\Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $listquiz= $quizRepository->findAll();


        $text = $request->get('typetext');


        $listparticipantquiz= $participantQuizRepository->findAll();

        $nbTentative=1;

        for($i=0; $i < count($listparticipantquiz)-1; $i++){


            if ($listparticipantquiz[$i]->getUser()==$this->getUser() && $listparticipantquiz[$i]->getQuiz()==$quiz ){
                $nbTentative=$nbTentative+1;
            }

        }

        if ($nbTentative >= $quiz->getNbTentative()){
            $this->render('front_end/index.html.twig',[
                'listquiz'=> $listquiz,
                'userconct' => $this->getUser(),
                'listparticipantquiz' => $listparticipantquiz
            ]);
        }

        else{
            $p=new ParticipantQuiz();

            $p->setQuiz($quiz);

            $p->setUser($this->getUser());

            $entityManager->persist($p);
            $entityManager->flush();


        }

        return $this->render('front_end/quizpublic.html.twig',[
            'quiz'=> $quiz,
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz,



        ]);

    }




    /**
     * @Route("/test", name="test", methods={"GET" , "POST"})
     */
    public function test(QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository)
    {
        $listquiz= $quizRepository->findAll();

        $listparticipantquiz= $participantQuizRepository->findAll();

        return $this->render('front_end/test.html.twig',[

            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz

        ]);
    }


}