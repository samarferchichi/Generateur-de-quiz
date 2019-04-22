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
use App\Entity\ReponseParticipant;
use App\Entity\User;
use App\Repository\PageRepository;
use App\Repository\ParticipantQuizRepository;
use App\Repository\QuizRepository;
use App\Repository\ReponseParticipantRepository;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;

use http\Env\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Tests\Fixtures\ToString;

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
     * @Route("/testaa", name="testa", methods={"GET" , "POST"})
     */
    public function testa( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository)
    {
        $listquiz= $quizRepository->findAll();
        $listparticipantquiz= $participantQuizRepository->findAll();



        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $user->addRole("ROLE_ADMIN");


        $em->persist($user);
        $em->flush();
        return $this->render('front_end/changerole.html.twig',[
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz
        ]);
    }



    /**
     * @Route("/testaa", name="changeAdmin", methods={"GET" , "POST"})
     */
    public function changeAdmin()
    {



        return $this->render('test');
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
     * @Route("/resultat/{quiz}", name="resultat", methods={"GET" , "POST"})
     */
    public function resultat( Quiz $quiz, ParticipantQuiz $p, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository ,ReponseParticipantRepository $reponseParticipantRepository, \Symfony\Component\HttpFoundation\Request $request)
    {


        $listquiz= $quizRepository->findAll();

        $entityManager = $this->getDoctrine()->getManager();

        $listparticipantquiz= $participantQuizRepository->findAll();
        $reponseParticipant = $reponseParticipantRepository->findAll();

        $max= max($reponseParticipant);




        $p->setResultat(1);
        $entityManager->persist($p);
        $entityManager->flush();

        return $this->render('front_end/resultat.html.twig',[
            'quiz'=> $quiz,
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz,
            'reponseParticipant' => $reponseParticipant,
            'max' => $max




        ]);

    }
    /**
     * @Route("/quizpublic/{quiz}", name="quizpublic", methods={"GET" , "POST"})
     */
    public function quizpublic( Quiz $quiz,ReponseParticipantRepository $reponseParticipantRepository, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository , \Symfony\Component\HttpFoundation\Request $request)
    {
        $t=1;
        $entityManager = $this->getDoctrine()->getManager();

        $listquiz= $quizRepository->findAll();


        $listparticipantquiz= $participantQuizRepository->findAll();





        $nbTentative=0;

        for($i=0; $i < count($listparticipantquiz); $i++){

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
            $p->setResultat(0);

            $entityManager->persist($p);
          //  $entityManager->flush();

            $text = $request->get('typetext');
            $nbtexte = $request->get('nbtext');


            if($text != null){

                $entityManager->flush();

                for ($i = 0; $i < count($nbtexte); $i++) {

                        $rep = new ReponseParticipant();


                        foreach ($quiz->getPage() as $q)
                            {foreach ($q->getQuestion() as $question) {
                                if ($question == $nbtexte[$i])
                                {

                                    $rep->setReponse($text[$i]);
                                    $rep->setParticipantquiz($p);
                                    $rep->setIdQuestion($question);
                                    $rep->setTentative($nbTentative+1+$p->getId());


                                    $entityManager->persist($rep);
                                    $entityManager->flush();


                                }
                            }

                            }

              }
/*
                $date = $request->get('date');
                $nbdate = $request->get('nbdate');

                for ($i = 0; $i < count($nbdate); $i++) {

                    $rep = new ReponseParticipant();


                    foreach ($quiz->getPage() as $q)
                    {foreach ($q->getQuestion() as $question) {
                        if ($question == $nbdate[$i])
                        {

                            $rep->setReponse($date[$i]);
                            $rep->setParticipantquiz($p);
                            $rep->setIdQuestion($question);
                            $rep->setTentative($nbTentative+1+$p->getId());

                            $entityManager->persist($rep);
                            $entityManager->flush();


                        }
                    }

                    }

                }*/






                return $this->redirectToRoute('resultat',[
                    'quiz'=> $quiz->getId(),
                    'text' => $text,
                    'p' => $p
                ]);


            }




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