<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 02/04/19
 * Time: 11:11
 */

namespace App\Controller\front_end;
use App\Entity\Participant;
use App\Entity\ParticipantQuiz;
use App\Entity\Quiz;
use App\Entity\ReponseParticipant;
use App\Entity\Resultat;
use App\Repository\ParticipantQuizRepository;
use App\Repository\ParticipantRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\ReponseParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/index")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET" , "POST"})
     */
    public function show( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository, ParticipantRepository $participantRepository)
    {
        $listquiz= $quizRepository->findAll();
        $listparticipantquiz= $participantQuizRepository->findAll();

        $participant = $participantRepository->findAll();



        return $this->render('front_end/index.html.twig',[
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz,
            'participant' =>$participant
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
     * @Route("/resultat/{quiz}/{par}/{tentative}", name="resultat", methods={"GET" , "POST"})
     */
    public function resultat( Quiz $quiz, $tentative  ,Participant $par,  QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository ,ReponseParticipantRepository $reponseParticipantRepository, \Symfony\Component\HttpFoundation\Request $request)

{

        $listquiz= $quizRepository->findAll();

        $entityManager = $this->getDoctrine()->getManager();

        $listparticipantquiz= $participantQuizRepository->findAll();
        $reponseParticipant = $reponseParticipantRepository->findAll();

       // $max= max($reponseParticipant);



      /*  $p->setResultat(1);
        $entityManager->persist($p);
        $entityManager->flush();*/

        return $this->render('front_end/resultat.html.twig',[
            'quiz'=> $quiz,
            'listquiz'=> $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz,
            'reponseParticipant' => $reponseParticipant,
            'par'=>$par,
            'tentative'=>$tentative





        ]);

    }




    /**
     * @Route("/listerquiz/{categorie}", name="listerquiz", methods={"GET" , "POST"})
     */
    public function listerquiz( String $categorie, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findAll();
        return $this->render('front_end/listercategorie.html.twig', ['categorie' => $categorie, 'listquiz' =>$listquiz]);
    }





    /**
     * @Route("/testsendmail/{quiz}", name="testsendmail", methods={"GET" , "POST"})
     */
    public function testsendmail( Quiz $quiz,ReponseParticipantRepository $reponseParticipantRepository, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository , \Symfony\Component\HttpFoundation\Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $listquiz= $quizRepository->findAll();


        $listparticipantquiz= $participantQuizRepository->findAll();


        return $this->render('front_end/testsendmail.html.twig',[
            'quiz'=>$quiz,
            'listquiz' => $listquiz,
            'listparticipantquiz' => $listparticipantquiz,
        ]);


    }





    /**
     * @Route("/quizpublic/{quiz}/{par}", name="quizpublic", methods={"GET" , "POST"})
     */
    public function quizpublic( Quiz $quiz,Participant $par,ReponseParticipantRepository $reponseParticipantRepository, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository, QuestionRepository $questionRepository, \Symfony\Component\HttpFoundation\Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $listquiz = $quizRepository->findAll();

        $listparticipantquiz = $participantQuizRepository->findAll();

        $form = $this->createFormBuilder(null, [
                        'action' => $this->generateUrl('quizpublic', [
                            'quiz' => $quiz->getId(),
                            'par' => $par->getId()
                        ])
                    ])
                     ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typevf = $request->get('typevf');
            $data = [];

            foreach ($typevf as $key => $tvf){
                $d = explode('+', $key);
                array_push($d, $tvf);
                array_push($data, $d);
            }

            $ParticipantQuiz = $participantQuizRepository->findBy(['participant' => $par->getId(), 'quiz' => $quiz->getId()]);
            if(count($ParticipantQuiz) == 1){
                $ParticipantQuiz[0]->setTentative($ParticipantQuiz[0]->getTentative() + 1);
                $ParticipantQuiz_new = $ParticipantQuiz[0];
            }else{
                $ParticipantQuiz_new = new ParticipantQuiz();

                $ParticipantQuiz_new->setTentative(1);
                $ParticipantQuiz_new->setParticipant($par);
                $ParticipantQuiz_new->setQuiz($quiz);
                $entityManager->persist($ParticipantQuiz_new);
            }

            $resultat =  new Resultat();

            $resultat->setParticipantQuiz($ParticipantQuiz_new);
            $resultat->setTentative($ParticipantQuiz_new->getTentative());
            $resultat->setResultat(20);

            $entityManager->persist($resultat);

            foreach ($data as $d){
                $rep_participant = new ReponseParticipant();

                $rep_participant->setResultat($resultat);
                $rep_participant->setReponse($d[2]);
                $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                $entityManager->persist($rep_participant);
            }

//            $entityManager->flush();

            exit();
        }

        //TODO => redirect to interface resultat with correction

        return $this->render('front_end/quizpublic.html.twig', [
            'quiz' => $quiz,
            'listquiz' => $listquiz,
            'userconct' => $this->getUser(),
            'listparticipantquiz' => $listparticipantquiz,
            'par' => $par,
            'form' => $form->createView()


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