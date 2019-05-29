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


/**
 * Common features needed in admin controllers.
 *
 * @internal
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class StatistiqueController extends EasyAdminController
{

    /**
     * @Route("/statistique", name="statistique")
     */
    public function statistique( QuizRepository $quizRepository, ParticipantQuizRepository $participantQuizRepository )
    {

        $participants = $participantQuizRepository->getParticipants($this->getUser()->getId());
        $tentatives = $participantQuizRepository->getMaxTentatives($this->getUser()->getId());

        dump($tentatives);
        exit();

        $data = [];

        foreach ($participants as $p){
            $d = [
                'name' => $p['titleQuiz'],
                'y'    => intval($p['NbParticipant'])
            ];
            array_push($data, $d);
        }

        return $this->render('quiz/statistique.html.twig',
            [
                'userquiz' => json_encode($data),
                'participant' => [],
            ]);
    }



}