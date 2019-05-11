<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Parametre;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Reponse;
use App\Form\ParametreType;
use App\Form\QuestionType;
use App\Form\ReponseType;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use PhpParser\Parser\Php7;
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


    /**
     * @Route("/question_importe/{question}/{quizactuel}/{pageactuel}", name="question_importe", methods={"GET" , "POST"})
     */
    public function question_importe(Request $request, Page $pageactuel, Quiz $quizactuel,ReponseRepository $reponseRepository, Question $question, Reponse $reponse): Response
    {

        if(count($question->getPage()->getQuestion()) < $question->getPage()->getQuiz()->getNbQuestion()){

            $entityManager = $this->getDoctrine()->getManager();

            $q = new Question();
            $q->setPage($pageactuel);
            $q->setTextQuestion($question->getTextQuestion());
            $q->setTypeQuestion($question->getTypeQuestion());
            if($question->getDescriptionQuestion())
                $q->setDescriptionQuestion($question->getDescriptionQuestion());
            if($question->getInfoBulle())
                $q->setInfoBulle($question->getInfoBulle());

            $q->setActif($question->getActif());

            $entityManager->persist($q);


            foreach ($question->getParametre() as $par){
                $p = new Parametre();

                if($par->getNbCaractere())
                    $p->setNbCaractere($par->getNbCaractere());
                if($par->getNbChiffre())
                    $p->setNbChiffre($par->getNbChiffre());
                if($par->getFormText())
                    $p->setFormText($par->getFormText());
                $p->setQuestion($q);

                $entityManager->persist($p);
            }

            foreach ($question->getReponse() as $rep){
                $r = new Reponse();

                if($rep->getDescriptiondate())
                    $r->setDescriptiondate($rep->getDescriptiondate());
                if($rep->getDescriptionformule())
                    $r->setDescriptionformule($rep->getDescriptionformule());
                if($rep->getResultatformule())
                    $r->setResultatformule($rep->getResultatformule());
                if($rep->getFormule())
                    $r->setFormule($rep->getFormule());
                if($rep->getEtatlist())
                    $r->setEtatlist($rep->getEtatlist());
                $r->setEtatcaseacocher($rep->getEtatcaseacocher());
                if($rep->getEtatcaseacocher())
                    $r->setEtatvf($rep->getEtatvf());
                if($rep->getReponseValide())
                    $r->setReponseValide($rep->getReponseValide());
                if($rep->getDesnumber())
                    $r->setDesnumber($rep->getDesnumber());

                $r->setQuestion($q);

                $entityManager->persist($r);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('creerquiz', ['id' => $quizactuel->getId(), 'page' => $pageactuel->getId()]);
    }






    public function questionDepAction(Request $request,ReponseRepository $reponseRepository, Question $question, Reponse $reponse): Response
    {

        if(count($question->getPage()->getQuestion()) < $question->getPage()->getQuiz()->getNbQuestion()){

            $entityManager = $this->getDoctrine()->getManager();

            $q = new Question();
            $q->setPage($question->getPage());
            $q->setTextQuestion($question->getTextQuestion());
            $q->setTypeQuestion($question->getTypeQuestion());
            if($question->getDescriptionQuestion())
                $q->setDescriptionQuestion($question->getDescriptionQuestion());
            if($question->getInfoBulle())
                $q->setInfoBulle($question->getInfoBulle());

            $q->setActif($question->getActif());

            $entityManager->persist($q);


            foreach ($question->getParametre() as $par){
                $p = new Parametre();

                if($par->getNbCaractere())
                    $p->setNbCaractere($par->getNbCaractere());
                if($par->getNbChiffre())
                    $p->setNbChiffre($par->getNbChiffre());
                if($par->getFormText())
                    $p->setFormText($par->getFormText());
                $p->setQuestion($q);

                $entityManager->persist($p);
            }

            foreach ($question->getReponse() as $rep){
                $r = new Reponse();

                if($rep->getDescriptiondate())
                    $r->setDescriptiondate($rep->getDescriptiondate());
                if($rep->getDescriptionformule())
                    $r->setDescriptionformule($rep->getDescriptionformule());
                if($rep->getResultatformule())
                    $r->setResultatformule($rep->getResultatformule());
                if($rep->getFormule())
                    $r->setFormule($rep->getFormule());
                if($rep->getEtatlist())
                    $r->setEtatlist($rep->getEtatlist());
                $r->setEtatcaseacocher($rep->getEtatcaseacocher());
                if($rep->getEtatcaseacocher())
                    $r->setEtatvf($rep->getEtatvf());
                if($rep->getReponseValide())
                    $r->setReponseValide($rep->getReponseValide());
                if($rep->getDesnumber())
                     $r->setDesnumber($rep->getDesnumber());

                $r->setQuestion($q);

                $entityManager->persist($r);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('creerquiz', ['id' => $question->getPage()->getQuiz()->getId(), 'page' => $question->getPage()->getId()]);
    }



    public function questionDepAction2(Request $request,ReponseRepository $reponseRepository, Question $question, Reponse $reponse): Response
    {

        if(count($question->getPage()->getQuestion()) < $question->getPage()->getQuiz()->getNbQuestion()){

            $entityManager = $this->getDoctrine()->getManager();

            $q = new Question();
            $q->setPage($question->getPage());
            $q->setTextQuestion($question->getTextQuestion());
            $q->setTypeQuestion($question->getTypeQuestion());
            if($question->getDescriptionQuestion())
                $q->setDescriptionQuestion($question->getDescriptionQuestion());
            if($question->getInfoBulle())
                $q->setInfoBulle($question->getInfoBulle());

            $q->setActif($question->getActif());

            $entityManager->persist($q);


            foreach ($question->getParametre() as $par){
                $p = new Parametre();

                if($par->getNbCaractere())
                    $p->setNbCaractere($par->getNbCaractere());
                if($par->getNbChiffre())
                    $p->setNbChiffre($par->getNbChiffre());
                if($par->getFormText())
                    $p->setFormText($par->getFormText());
                $p->setQuestion($q);

                $entityManager->persist($p);
            }

            foreach ($question->getReponse() as $rep){
                $r = new Reponse();

                if($rep->getDescriptiondate())
                    $r->setDescriptiondate($rep->getDescriptiondate());
                if($rep->getDescriptionformule())
                    $r->setDescriptionformule($rep->getDescriptionformule());
                if($rep->getResultatformule())
                    $r->setResultatformule($rep->getResultatformule());
                if($rep->getFormule())
                    $r->setFormule($rep->getFormule());
                if($rep->getEtatlist())
                    $r->setEtatlist($rep->getEtatlist());
                $r->setEtatcaseacocher($rep->getEtatcaseacocher());
                if($rep->getEtatcaseacocher())
                    $r->setEtatvf($rep->getEtatvf());
                if($rep->getReponseValide())
                    $r->setReponseValide($rep->getReponseValide());
                if($rep->getDesnumber())
                    $r->setDesnumber($rep->getDesnumber());


                $r->setQuestion($q);

                $entityManager->persist($r);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('modifier_page', ['quiz' => $question->getPage()->getQuiz()->getId(), 'page' => $question->getPage()->getId()]);
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


    public function questionActif2Action(Request $request, Question $question): Response
    {
        $page=$question->getPage()->getId();
        $quiz=$question->getPage()->getQuiz()->getId();

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

        return $this->redirectToRoute('modifier_page', ['quiz' => $quiz, 'page' => $page]);
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
            'action' => $this->generateUrl('question_edit', ['id' => $question->getId() ])

        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid() ) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('creerquiz', [
                'id' => $question->getPage()->getQuiz()->getId(),
                'page' => $question->getPage()->getId(),
            ]);
        }


        return $this->render('question/editQuestion.html.twig', [
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
            ->getForm();
        $page = $question->getPage();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();

            return $this->redirectToRoute('creerquiz', [
                'id' => $page->getQuiz()->getId(),
                'page' => $page->getId()
            ]);
        }

        return $this->render('question/_delete_form.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/{id}/delete2", name="question_delete2", methods={"GET","POST", "DELETE"})
     */
    public function delete2(Request $request, Question $question): Response
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('question_delete2', ['id' => $question->getId()])
        ])
            ->getForm();

        $page = $question->getPage();
        $quiz=$page->getQuiz()->getId();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();

            return $this->redirectToRoute('modifier_page', [
                'quiz' => $page->getQuiz()->getId(),
                'page' => $page->getId(),
            ]);
        }

        return $this->render('question/_delete_form.html.twig', [
            'quiz' => $quiz,
            'page' => $page,
            'form' => $form->createView(),
        ]);

    }





}
