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
use App\Entity\Rate;
use App\Entity\ReponseParticipant;
use App\Entity\Resultat;
use App\Repository\ParticipantQuizRepository;
use App\Repository\ParticipantRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\RateRepository;
use App\Repository\ReponseParticipantRepository;
use App\Repository\ResultatRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function resultat( Quiz $quiz, $tentative  ,Participant $par,ParticipantQuizRepository $participantQuizRepository, ReponseParticipantRepository $reponseParticipantRepository, ResultatRepository $resultatrepository, ReponseRepository $reponseRepository, RateRepository $rateRepository, QuizRepository $quizRepository, \Symfony\Component\HttpFoundation\Request $request){

//        $quiz->setModeCorrection(false);
        $rate = $rateRepository->findBy(['quiz' => $quiz->getId(), 'participant' => $par->getId()]);

        if($quiz->getModeCorrection()){

            $participantquiz = $participantQuizRepository->findBy(['quiz' => $quiz->getId(), 'participant'=>$par->getId()]);
            $resultat = $resultatrepository->findBy(['participantquiz' => $participantquiz, 'tentative' => $tentative]);
            $reponseparticipant = $reponseParticipantRepository->findBy(['resultat' => $resultat]);

            $data = [];
            $data_question = [];
            $data_score = [];
            $score = 0;

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
                    array_push($data_question, $q);
                }
                array_push($data, $d);
            }

            foreach ($data_question as $d){

                if($d['type_question'] == 'Vrai/faux'){
                    $total = count($d['reponsesQuestion']);
                    $totalValid = 0;
                    foreach ($d['reponsesQuestion'] as $key => $repQuest){
                        $repQuestPart = ($d['reponsesParticipant'][$key]['reponse'] == 'Vrai') ? true : false;
                        if($repQuestPart == $repQuest['etatvf'])
                            $totalValid++;
                    }

                }elseif($d['type_question'] == 'Case à cocher'){
                    $total = count($d['reponsesQuestion']);
                    $totalValid = 0;
                    foreach ($d['reponseValid'] as $repValid){
                        if($repValid['etatcaseacocher'] == $repValid['repParticipant'])
                            $totalValid++;
                    }

                }elseif($d['type_question'] == 'Liste déroulante'){
                    $total = 1;
                    $totalValid = 0;
                    $repValid = null;
                    foreach ($d['reponsesQuestion'] as $repQuest){
                        if($repQuest['etatlist'] == true)
                            $repValid = $repQuest['reponse_valide'];
                    }
                    if($repValid == $d['reponsesParticipant'][0]['reponse'])
                        $totalValid++;

                }elseif($d['type_question'] == 'Date'){
                    $total = count($d['reponsesQuestion']);
                    $totalValid = 0;
                    foreach ($d['reponsesQuestion'] as $key => $repQuest){
                        if($repQuest['reponse_valide'] == $d['reponsesParticipant'][$key]['reponse'])
                            $totalValid++;
                    }

                }elseif($d['type_question'] == 'Nombre'){
                    $total = count($d['reponsesQuestion']);
                    $totalValid = 0;
                    foreach ($d['reponsesQuestion'] as $key => $repQuest){
                        if($repQuest['reponse_valide'] == $d['reponsesParticipant'][$key]['reponse'])
                            $totalValid++;
                    }

                }elseif($d['type_question'] == 'Calculée'){
                    $total = 1;
                    $totalValid = 0;
                }

                $d = [
                    'type' => $d['type_question'],
                    'total' => $total,
                    'totalValid' => $totalValid,
                ];
                array_push($data_score, $d);
            }

            foreach ($data_score as $d_s){
                $score += round((($d_s['totalValid']*100/$d_s['total'])/100), 2);
            }

            return $this->render('front_end/resultat_correction_enable.html.twig',[
                'quiz'=> $quiz,
                'userconct' => $this->getUser(),
                'par'=>$par,
                'tentative'=>$tentative,
                'reponseparticipant' => $reponseparticipant,
                'resultat' =>$resultat,
                'pages' => $data,
                'rate' => $rate ? $rate[0] : null,
                'totale' => count($data_score),
                'score' => $score
            ]);
        }else{

            return $this->render('front_end/resultat_correction_disable.html.twig',[
                'quiz'=> $quiz,
                'par'=>$par,
                'rate' => $rate ? $rate[0] : null
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
     * @Route("/rate/Quiz/{quiz}/{participant}/{rate}", name="rate_quiz", methods={"POST"})
     */
    public function rateQuiz(Quiz $quiz, Participant $participant, $rate)
    {
        $em = $this->getDoctrine()->getManager();
        $rateObject = new Rate();
        $rateObject->setQuiz($quiz);
        $rateObject->setParticipant($participant);
        $rateObject->setRate($rate);
        $em->persist($rateObject);

        $em->flush();

        return new JsonResponse(true);
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