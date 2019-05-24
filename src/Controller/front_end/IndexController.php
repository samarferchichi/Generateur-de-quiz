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
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\ReponseParticipant;
use App\Entity\Resultat;
use App\Entity\User;
use App\Form\ParticipantType;
use App\Repository\PageRepository;
use App\Repository\ParticipantQuizRepository;
use App\Repository\ParticipantRepository;
use App\Repository\QuizRepository;
use App\Repository\ReponseParticipantRepository;
use Doctrine\DBAL\Types\IntegerType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;

use http\Env\Request;
use http\Env\Response;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Scalar\String_;
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
    public function quizpublic( Quiz $quiz,Participant $par,ReponseParticipantRepository $reponseParticipantRepository, QuizRepository $quizRepository,ParticipantQuizRepository $participantQuizRepository , \Symfony\Component\HttpFoundation\Request $request)
    {


        $entityManager = $this->getDoctrine()->getManager();

        $listquiz= $quizRepository->findAll();


        $listparticipantquiz= $participantQuizRepository->findAll();

        $total=0;

        $intro=null;

        foreach ($listparticipantquiz as $p){

            if ($p->getParticipant()== $par && $p->getQuiz() == $quiz){
                $p->setTentative($p->getTentative()+1);

                $resultat =  new Resultat();
                $resultat->setParticipantQuiz($p);
                $resultat->setTentative($p->getTentative());
                $resultat->setResultat(20);

                $entityManager->persist($resultat);


                $reponseparticipant = new ReponseParticipant();
                $reponseparticipant->setResultat($resultat);

                $intro =1;
            }else{
                $intro =0;

            }
        }


        if ($intro == 0){
            $participantquiz = new ParticipantQuiz();



            $participantquiz->setParticipant($par);
            $participantquiz->setQuiz($quiz);
            $participantquiz->setTentative(1);

            $resultat =  new Resultat();

            $resultat->setParticipantQuiz($participantquiz);
            $resultat->setTentative($participantquiz->getTentative());
            $resultat->setResultat(20);

            $entityManager->persist($resultat);

            $entityManager->persist($participantquiz);

            $reponseparticipant = new ReponseParticipant();
            $reponseparticipant->setResultat($resultat);

        }

        $vf = $request->get('typevf');
        $que = $request->get('question');



        $findquestion =new Question();



        if ($vf != null){

                foreach ($quiz->getPage() as $q) {
                    foreach ($q->getQuestion() as $question) {

                        foreach ($que as $qq) {


                            foreach ($vf as $rep) {
                                if($question->getId() == $qq)
                                    $findquestion=$question;


                                $reponseparticipant->setIdQuestion($findquestion);
                                $reponseparticipant->setReponse($rep);
                                $entityManager->persist($reponseparticipant);
                            }

                                }
                    }
                }


            $entityManager->flush();

                return $this->redirectToRoute('resultat',[
                    'quiz'=> $quiz->getId(),
                    'par'=>$par->getId(),
                    'tentative'=>$total
                ]);
            }


    return $this->render('front_end/quizpublic.html.twig', [
        'quiz' => $quiz,
        'listquiz' => $listquiz,
        'userconct' => $this->getUser(),
        'listparticipantquiz' => $listparticipantquiz,
        'par' => $par


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