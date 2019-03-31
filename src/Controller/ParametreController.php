<?php

namespace App\Controller;

use App\Entity\Parametre;
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
     * @Route("/{id}/edit/parametre", name="parametre_edit", methods={"GET","POST"})
     */
    public function editparametre(Request $request, Parametre $parametre): Response
    {
        $form = $this->createForm(ParametreType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parametre_index', [
                'id' => $parametre->getId(),
            ]);
        }

        return $this->render('parametre/edit.html.twig', [
            'parametre' => $parametre,
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
