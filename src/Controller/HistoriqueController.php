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
     * @Route("/recuperer/{participantquiz}/{tentative}", name="recuperer", methods={"GET", "POST"})
     */
    public function recuperer(ParticipantQuiz $participantQuiz, $tentative, ResultatRepository $resultatRepository, ReponseParticipantRepository $reponseParticipantRepository)
    {
        $allresultat = $resultatRepository->findAll();
        $allreponse = $reponseParticipantRepository->findAll();
        return $this->render('quiz/showdetail.html.twig', [
            'tentative' => $tentative,
            'participantquiz' => $participantQuiz,
            'allresultat' => $allresultat,
            'allreponse' => $allreponse
        ]);
    }


}
