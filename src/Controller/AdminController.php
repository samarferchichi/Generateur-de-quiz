<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Common features needed in admin controllers.
 *
 * @internal
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class AdminController extends EasyAdminController
{

    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('bundles/easy_admin/dashboard.html.twig');
    }


    /**
     * @Route("/creerquiz", name="creerquiz")
     */
    public function creerquizAction()
    {
        return $this->render('quiz/creerquiz.html.twig');
    }


    /**
     * @Route("/new", name="quiz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('creerquiz');
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quiz_index", name="quiz_index", methods={"GET"})
     */
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }


}
