<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reponse")
 */
class ReponseController extends AbstractController
{
    /**
     * @Route("/", name="reponse_index", methods={"GET"})
     */
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="reponse_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('reponse_index');
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reponse_show", methods={"GET"})
     */
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    /**
     * @Route("/{id}/editReponse", name="reponse_edit", methods={"GET","POST"})
     */
    public function editReponse(Request $request, Reponse $reponse): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse, [
            'action' => $this->generateUrl('reponse_edit', ['id' => $reponse->getId()])
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $page = $reponse->getQuestion()->getPage()->getId();

            $quiz= $reponse->getQuestion()->getPage()->getQuiz()->getId();


            return $this->redirectToRoute('creerquiz', [
                'id'=>$quiz,
                'page' => $page
            ]);
        }


        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'question' => $reponse->getQuestion(),
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/{id}/editReponse2", name="reponse_edit2", methods={"GET","POST"})
     */
    public function editReponse2(Request $request, Reponse $reponse): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse, [
            'action' => $this->generateUrl('reponse_edit2', ['id' => $reponse->getId()])
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $page = $reponse->getQuestion()->getPage()->getId();

            $quiz= $reponse->getQuestion()->getPage()->getQuiz()->getId();


            return $this->redirectToRoute('modifier_page', [
                'quiz'=>$quiz,
                'page' => $page
            ]);
        }


        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'question' => $reponse->getQuestion(),
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("/{id}", name="reponse_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reponse $reponse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reponse_index');
    }
}
