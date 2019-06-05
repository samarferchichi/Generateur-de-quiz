<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 05/05/19
 * Time: 23:06
 */

namespace App\Controller;


use App\Entity\Page;
use App\Entity\Parametre;
use App\Entity\ParticipantQuiz;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\PageType;
use App\Form\ParametreType;
use App\Form\QuestionType;
use App\Repository\PageRepository;
use App\Repository\ParametreRepository;
use App\Repository\ParticipantQuizRepository;
use App\Repository\QuestionRepository;
use App\Repository\RateRepository;
use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Common features needed in admin controllers.
 *
 * @internal
 *
 * @Security("is_granted('ROLE_USER')")
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class StatistiqueController extends EasyAdminController
{

    /**
     * @Route("/statistique", name="statistique")
     */
    public function statistique( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository, RateRepository $rateRepository )
    {

        $participants = $participantQuizRepository->getParticipants($this->getUser()->getId());
        $tentatives = $participantQuizRepository->getMaxTentatives($this->getUser()->getId());
        $ratings = $rateRepository->getRatingByUser($this->getUser()->getId());

        $total_ratings = $rateRepository->getTotalRatingByUser($this->getUser()->getId());
        $total_participants = $participantQuizRepository->getTotalParticipants($this->getUser()->getId());

        $data = [];
        $data2 = [];
        $rating_names = [];
        $rating_notes = [];

        foreach ($participants as $p){
            $d = [
                'name' => $p['titleQuiz'],
                'y'    => intval($p['NbParticipant'])
            ];
            array_push($data, $d);
        }

        foreach ($tentatives as $key => $t){
            $d = [
                'name' => $t['titleQuiz'],
                'y'    => intval($t['NbTentatives'])
            ];
            if($key == 0){
                $d['sliced'] =  true;
            }
            array_push($data2, $d);
        }

        foreach ($ratings as $r){
            array_push($rating_names, $r['titleQuiz']);
            array_push($rating_notes, $r['rate']/$r['total']);
        }

        return $this->render('quiz/statistique.html.twig',
            [
                'userquiz' => json_encode($data),
                'tentativesquiz' => json_encode($data2),
                'ratings_names' => json_encode($rating_names),
                'ratings_notes' => json_encode($rating_notes),
                'total_ratings' => ((int)$total_ratings[0]['rate'] != 0) ? ((int)$total_ratings[0]['rate']/(int)$total_ratings[0]['total']) : 0,
                'total_participants' => $total_participants[0]['NbParticipant']
            ]);
    }



}