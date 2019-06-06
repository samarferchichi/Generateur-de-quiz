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
        $data = [];

        $quiz=$participantquiz->getQuiz();
            $par=$participantquiz->getParticipant();
        $resultat = $resultatrepository->findBy(['participantquiz' => $participantquiz->getId(), 'tentative' => $tentative]);
        $reponseparticipant = $reponseparticipantrepository->findBy(['resultat' => $resultat[0]->getId()]);


            $participantquiz = $participantQuizRepository->findBy(['quiz' => $quiz->getId(), 'participant'=>$par->getId()]);
            $resultat = $resultatrepository->findBy(['participantquiz' => $participantquiz, 'tentative' => $tentative]);
            $reponseparticipant = $reponseParticipantRepository->findBy(['resultat' => $resultat]);


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
            

            return $this->render('quiz/showDetail.html.twig',[
                'quiz'=> $quiz,
                'userconct' => $this->getUser(),
                'par'=>$par,
                'tentative'=>$tentative,
                'reponseparticipant' => $reponseparticipant,
                'resultat' =>$resultat,
                'pages' => $data,
            ]);
        }







        return $this->render('quiz/showDetail.html.twig', [
            'p' => $participantquiz,
            't' => $tentative,
            "reponseparticipant" => $reponseparticipant[0],
            "resultat" => $resultat[0]
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
