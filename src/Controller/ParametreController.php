<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Parametre;
use App\Entity\Quiz;
use App\Form\ParametreType;
use App\Repository\ParametreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/parametre")
 */
class ParametreController extends AbstractController
{
    /**
     * @Route("/", name="parametre_index", methods={"GET"})
     */
    public function index(ParametreRepository $parametreRepository): Response
    {
        return $this->render('parametre/index.html.twig', [
            'parametres' => $parametreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="parametre_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $parametre = new Parametre();
        $form = $this->createForm(ParametreType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($parametre);
            $entityManager->flush();

            return $this->redirectToRoute('parametre_index');
        }

        return $this->render('parametre/new.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="parametre_show", methods={"GET"})
     */
    public function show(Parametre $parametre): Response
    {
        return $this->render('parametre/show.html.twig', [
            'parametre' => $parametre,
        ]);
    }


    /**
     * @Route("/{id}/editParametre", name="parametre_edit", methods={"GET","POST"})
     */
    public function editparametre(Request $request, Parametre $parametre): Response
    {
        $form = $this->createForm(ParametreType::class, $parametre, [
            'action' => $this->generateUrl('parametre_edit', ['id' => $parametre->getId()])
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $page = $parametre->getQuestion()->getPage()->getId();

            $quiz= $parametre->getQuestion()->getPage()->getQuiz()->getId();


            return $this->redirectToRoute('creerquiz', [
                'id'=>$quiz,
                'page' => $page
            ]);
        }

        return $this->render('parametre/edit.html.twig', [
            'parametre' => $parametre,
            'question' => $parametre->getQuestion(),
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/editParametre2", name="parametre_edit2", methods={"GET","POST"})
     */
    public function editparametre2(Request $request, Parametre $parametre): Response
    {
        $form = $this->createForm(ParametreType::class, $parametre, [
            'action' => $this->generateUrl('parametre_edit2', ['id' => $parametre->getId()])
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $page = $parametre->getQuestion()->getPage()->getId();

            $quiz= $parametre->getQuestion()->getPage()->getQuiz()->getId();


            return $this->redirectToRoute('modifier_page', [
                'quiz'=>$quiz,
                'page' => $page
            ]);
        }

        return $this->render('parametre/edit.html.twig', [
            'parametre' => $parametre,
            'question' => $parametre->getQuestion(),
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("/{id}", name="parametre_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Parametre $parametre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parametre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($parametre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('parametre_index');
    }
}
