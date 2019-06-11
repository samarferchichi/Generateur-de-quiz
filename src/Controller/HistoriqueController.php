<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 27/04/19
 * Time: 20:58
 */

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\ParticipantQuiz;
use App\Entity\Quiz;
use App\Entity\ReponseParticipant;
use App\Repository\ParticipantQuizRepository;
use App\Repository\ReponseParticipantRepository;
use App\Repository\ResultatRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Routing\Annotation\Route;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Repository\QuizRepository;

use App\Repository\ReponseRepository;

/**
 * Common features needed in admin controllers.
 *
 * @internal
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class HistoriqueController  extends EasyAdminController
{


    /**
     * @Route("/historique", name="historique")
     */
    public function dashboardAction(ParticipantQuizRepository $participantQuizRepository)
    {
        $parquiz = $participantQuizRepository->findAll();

        $user = $this->getUser();
        return $this->render('quiz/historiques.html.twig',['participant' =>$parquiz
        , 'user' => $user]);
    }

    /**
     * @Route("/showHistorique/{p}", name="showHistorique")
     */
    public function showHistorique(ParticipantQuiz $p)
    {

        return $this->render('quiz/showHistorique.html.twig',['p' => $p]);
    }



    /**
     * @Route("/infoparticipant/{participantquiz}/{tentative}", name="infoparticipant")
     */
    public function infoparticipant(ParticipantQuiz $participantquiz, $tentative, ReponseParticipantRepository $reponseparticipantrepository ,ParticipantQuizRepository $participantQuizRepository, ReponseParticipantRepository $reponseParticipantRepository, ResultatRepository $resultatrepository, ReponseRepository $reponseRepository, QuizRepository $quizRepository, \Symfony\Component\HttpFoundation\Request $request)
    {

        $quiz = $participantquiz->getQuiz();
        $par = $participantquiz->getParticipant();

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

        return $this->render('quiz/showDetail.html.twig', [
            'quiz'=> $quiz,
            'userconct' => $this->getUser(),
            'par'=>$par,
            'tentative'=>$tentative,
            'reponseparticipant' => $reponseparticipant,
            'resultat' =>$resultat,
            'pages' => $data,
            'totale' => count($data_score),
            'score' => $score
        ]);
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





}
