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
use App\Repository\ResultatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReponseRepository;

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
    public function resultat( Quiz $quiz, $tentative  ,Participant $par,ParticipantQuizRepository $participantQuizRepository, ReponseParticipantRepository $reponseParticipantRepository, ResultatRepository $resultatrepository, ReponseRepository $reponseRepository, QuizRepository $quizRepository, \Symfony\Component\HttpFoundation\Request $request){

        if($quiz->getModeCorrection()){

            $participantquiz = $participantQuizRepository->findBy(['quiz' => $quiz->getId(), 'participant'=>$par->getId()]);
            $resultat = $resultatrepository->findBy(['participantquiz' => $participantquiz, 'tentative' => $tentative]);
            $reponseparticipant = $reponseParticipantRepository->findBy(['resultat' => $resultat]);

            $data = [];

            foreach ($quiz->getPage() as $page){
                $d = [];
                foreach ($page->getQuestion() as $question){
                    $reponsesQuestion = $reponseRepository->getResultForQuestion($question->getId());
                    $reponsesParticipant = $reponseParticipantRepository->getReponseForQuestion($question->getId(), $par->getId(), $tentative);
                    if($question->getTypeQuestion() != 'Case à cocher'){
                        $q = [
                            'id' => $question->getId(),
                            'type_question' => $question->getTypeQuestion(),
                            'text_question' => $question->getTextQuestion(),
                            'actif' => $question->getActif(),
                            'reponsesQuestion' => $reponsesQuestion,
                            'reponsesParticipant' => $reponsesParticipant
                        ];
                    }else{

                        $data_reponsesParticipant = [];

                        for($i=0; $i<count($reponsesQuestion); $i++){
                            if($result = $this->_searchByOrder($reponsesParticipant, $i)){
                                array_push($data_reponsesParticipant, $result);
                            }else{
                                array_push($data_reponsesParticipant, [
                                    "id" => null,
                                    "ordre" => $i,
                                    "reponse" => false
                                ]);
                            }
                        }

                        $data_reponsesValid = [];

                        foreach ($reponsesQuestion as $key => $rep){
                            array_push($data_reponsesValid, [
                                'reponse_valide' => $rep['reponse_valide'],
                                'etatcaseacocher' => $rep['etatcaseacocher'],
                                'repParticipant' => $data_reponsesParticipant[$key]['reponse']
                            ]);
                        }

                        $q = [
                            'id' => $question->getId(),
                            'type_question' => $question->getTypeQuestion(),
                            'text_question' => $question->getTextQuestion(),
                            'actif' => $question->getActif(),
                            'reponsesQuestion' => $reponsesQuestion,
                            'reponsesParticipant' => $data_reponsesParticipant,
                            'reponseValid' => $data_reponsesValid
                        ];
                    }


                    array_push($d, $q);
                }
                array_push($data, $d);
            }

            return $this->render('front_end/resultat_correction_enable.html.twig',[
                'quiz'=> $quiz,
                'userconct' => $this->getUser(),
                'par'=>$par,
                'tentative'=>$tentative,
                'reponseparticipant' => $reponseparticipant,
                'resultat' =>$resultat,
                'pages' => $data
            ]);
        }else{

            return $this->render('front_end/resultat_correction_disable.html.twig',[
                'quiz'=> $quiz,
            ]);
        }


    }

    private function _searchByOrder($array, $order){
        $i = 0;
        $result = null;

        while ($i < count($array) && $result == null){
            if($array[$i]['ordre'] == $order){
                $result = $array[$i];
            }
            $i++;
        }

        if ($result)
            $result['reponse'] = true;

        return $result;
    }




    /**
     * @Route("/listerquiz/{categorie}", name="listerquiz", methods={"GET" , "POST"})
     */
    public function listerquiz( String $categorie, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findBy(['categorie' => $categorie]);
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
            /* -------------------------------------------------------------------------------------------------------------------------------- */
            /* ----------- Nouveau ou ancient participent (Si ancient augmenter n° tentative sinon nouveau Participant Quiz) ------------------ */
            /* -------------------------------------------------------------------------------------------------------------------------------- */
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
            /* -------------------------------------------------------------------------------------------------------------------------------- */
            /* -------------------------------------------------------------------------------------------------------------------------------- */

            /*  Récuperation et sauvgarde des données TypeVF */
            $typevf = $request->get('typevf');
           if($typevf != null){
               $data = [];

               foreach ($typevf as $key => $tvf){
                   $d = explode('+', $key);
                   array_push($d, $tvf);
                   array_push($data, $d);
               }

               foreach ($data as $d){
                   $rep_participant = new ReponseParticipant();

                   $rep_participant->setResultat($resultat);
                   $rep_participant->setReponse($d[2]);
                   $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                   $rep_participant->setOrdre($d[1]);
                   $entityManager->persist($rep_participant);
               }
           }
               /*  --------------------------------------------- */
               /*  --------------------------------------------- */



            /*  Récuperation et sauvgarde des données Type case a coucher */

            $typecaseacouche = $request->get('typecaseacouche');
            if($typecaseacouche != null) {

                $data = [];

                foreach ($typecaseacouche as $key => $tcase) {
                    $d = explode('+', $key);
                    array_push($d, $tcase);
                    array_push($data, $d);
                }

                foreach ($data as $d){
                    $rep_participant = new ReponseParticipant();

                    $rep_participant->setResultat($resultat);
                    $rep_participant->setReponse($d[2]);
                    $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                    $rep_participant->setOrdre($d[1]);
                    $entityManager->persist($rep_participant);
                }


            }
                /*  --------------------------------------------- */
                /*  --------------------------------------------- */



            /*  Récuperation et sauvgarde des données Type list deroulante */
                 $typelist = $request->get('typelist');
            if($typelist != null) {

                $data = [];

                foreach ($typelist as $key => $tlist) {
                    $d = explode('+', $key);
                    array_push($d, $tlist);
                    array_push($data, $d);
                }
                foreach ($data as $d){
                    $rep_participant = new ReponseParticipant();

                    $rep_participant->setResultat($resultat);
                    $rep_participant->setReponse($d[2]);
                    $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                    $rep_participant->setOrdre($d[1]);
                    $entityManager->persist($rep_participant);
                }

            }

            /*  --------------------------------------------- */
            /*  --------------------------------------------- */



            /*  Récuperation et sauvgarde des données Type date */
            $typedate = $request->get('typedate');

            if($typedate != null) {

                $data = [];

                foreach ($typedate as $key => $tdate) {
                    $d = explode('+', $key);
                    array_push($d, $tdate);
                    array_push($data, $d);
                }
                foreach ($data as $d){
                    $rep_participant = new ReponseParticipant();

                    $rep_participant->setResultat($resultat);
                    $rep_participant->setReponse($d[2]);
                    $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                    $rep_participant->setOrdre($d[1]);
                    $entityManager->persist($rep_participant);
                }

            }
            /*  --------------------------------------------- */
            /*  --------------------------------------------- */



            /*  Récuperation et sauvgarde des données Type date */
            $typenumber = $request->get('typenumber');

            if($typenumber != null) {

                $data = [];

                foreach ($typenumber as $key => $tnumber) {
                    $d = explode('+', $key);
                    array_push($d, $tnumber);
                    array_push($data, $d);
                }
                foreach ($data as $d){
                    $rep_participant = new ReponseParticipant();

                    $rep_participant->setResultat($resultat);
                    $rep_participant->setReponse($d[2]);
                    $rep_participant->setIdQuestion($questionRepository->find($d[0]));
                    $rep_participant->setOrdre($d[1]);
                    $entityManager->persist($rep_participant);
                }

            }
            /*  --------------------------------------------- */
            /*  --------------------------------------------- */




            $entityManager->flush();
            return $this->redirectToRoute('resultat', [
                'quiz' => $quiz->getId(),
                'par' => $par->getId(),
                'tentative' => $ParticipantQuiz_new->getTentative()
            ]);
        }

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