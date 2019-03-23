<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="question_index", methods={"GET"})
     */
    public function index(QuestionRepository $questionRepository): Response
    {
        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    public function questionDepAction(Request $request, Question $question): Response
    {

        if(count($question->getPage()->getQuestion()) < $question->getPage()->getQuiz()->getNbQuestion()){
            $q = new Question();
            $q->setPage($question->getPage());
            $q->setTextQuestion($question->getTextQuestion());
            $q->setTypeQuestion($question->getTypeQuestion());
            if($question->getDescriptionQuestion())
                $q->setDescriptionQuestion($question->getDescriptionQuestion());
            if($question->getInfoBulle())
                $q->setInfoBulle($question->getInfoBulle());

            $entityManager = $this->getDoctrine()->getManager();
            $question->setActif(true);
            $entityManager->persist($q);
            $entityManager->flush();
        }

        return $this->redirectToRoute('creerquiz', ['id' => $question->getPage()->getQuiz()->getId(), 'page' => $question->getPage()->getId()]);
    }




    public function questionActifAction(Request $request, Question $question): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
            if($question->getActif()==true)
            {

                $question->setActif(0);
                $entityManager->persist($question);
                $entityManager->flush();
            }
            else{
                if ($question->getActif()==false)
                {   $question->setActif(1);
                    $entityManager->persist($question);
                    $entityManager->flush();

                }
            }


        return $this->redirectToRoute('creerquiz', ['id' => $question->getPage()->getQuiz()->getId(), 'page' => $question->getPage()->getId()]);
    }



    /**
     * @Route("/{id}/show", name="question_show", methods={"GET"})
     */
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }



    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question, [
            'action' => $this->generateUrl('question_edit', ['id' => $question->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('creerquiz', [
                'id' => $question->getPage()->getQuiz()->getId(),
                'page' => $question->getPage()->getId()
            ]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/delete", name="question_delete", methods={"GET","POST", "DELETE"})
     */
    public function delete(Request $request, Question $question): Response
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('question_delete', ['id' => $question->getId()])
        ])
            ->add('submit', SubmitType::class)
                     ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            exit;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();

            return $this->redirectToRoute('creerquiz', [
                'id' => $question->getPage()->getQuiz()->getId(),
                'page' => $question->getPage()->getId()
            ]);
        }

        return $this->render('question/_delete_form.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }
}
