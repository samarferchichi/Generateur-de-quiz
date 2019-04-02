<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Quiz;
use App\Form\PageType;
use App\Form\QuizType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/", name="page_index", methods={"GET"})
     */
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }






    /**
     * @Route("/{id}", name="page_show", methods={"GET"})
     */
    public function show(Page $page): Response
    {
        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('page_index', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render('page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/delete", name="page_supprimer", methods={"GET","POST", "DELETE"})
     */
    public function deletepage(Request $request, Page $page): Response
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('page_supprimer', ['id' => $page->getId()])
        ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data_ordre = [''];

            foreach ($page->getQuiz()->getPage() as $p){
                array_push($data_ordre, $p->getOrdre());
            }
            $pos = array_search($page->getOrdre(), $data_ordre);

            if(count($page->getQuiz()->getPage()) == 1){
                $p = new Page();
                $p->setOrdre($page->getOrdre()+1);
                $p->setQuiz($page->getQuiz());
                $em->persist($p);
                $em->remove($page);
                $em->flush();

                return $this->redirectToRoute('creerquiz', [
                    'id' => $p->getQuiz()->getId(),
                    'page' => $p->getId()
                ]);
            }else{
                $page->getQuiz()->setNbPage($page->getQuiz()->getNbPage()-1);
                $em->remove($page->getQuiz()->getPage()[$pos - 1]);
                $em->flush();

                $p = $page->getQuiz()->getPage()[$pos - 2];

                return $this->redirectToRoute('creerquiz', [
                    'id' => $p->getQuiz()->getId(),
                    'page' => $p->getId()
                ]);

            }

        }

        return $this->render('page/_delete_form.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }




}





