<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 02/04/19
 * Time: 11:11
 */

namespace App\Controller\front_end;
use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/index")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function show( QuizRepository $quizRepository)
    {
        $listquiz= $quizRepository->findAll();

        return $this->render('front_end/index.html.twig',[
            'listquiz'=> $listquiz
        ]);

    }

    /**
     * @Route("/{quiz}", name="quizpublic", methods={"GET"})
     */
    public function quizpublic( Quiz $quiz, QuizRepository $quizRepository)
    {
        $listquiz= $quizRepository->findAll();

        return $this->render('front_end/quizpublic.html.twig',[
            'quiz'=> $quiz,
            'listquiz'=> $listquiz

        ]);

    }

}