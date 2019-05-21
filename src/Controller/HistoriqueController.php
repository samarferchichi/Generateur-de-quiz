<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 27/04/19
 * Time: 20:58
 */

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Quiz;
use App\Repository\ParticipantQuizRepository;
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
     * @Route("/showHistorique/{quiz}/{par}/{tentative}", name="showHistorique")
     */
    public function showHistorique(Quiz $quiz, Participant $par, $tentative, ParticipantQuizRepository $participantQuizRepository)
    {
        $parquiz = $participantQuizRepository->findAll();

        $user = $this->getUser();
        return $this->render('quiz/showHistorique.html.twig',['quiz' => $quiz, 'par' => $par, 'tentative' => $tentative]);
    }


}
