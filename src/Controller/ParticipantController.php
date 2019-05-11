<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Quiz;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/newparticipant/{quiz}/", name="participant_new", methods={"GET","POST"})
     */
    public function newparticipant(Request $request, Quiz $quiz, ParticipantRepository $participantRepository): Response
    {
        $participant = new Participant();

        $allparticipant = $participantRepository->findAll();

        $form = $this->createForm(ParticipantType::class, $participant,
            ['action' => $this->generateUrl('participant_new', ['quiz' => $quiz->getId(),'par' => $participant->getId()])]);

        $nb="";

        $form->handleRequest($request);
        foreach ($allparticipant as $p) {

                if ($p->getEmail() == $participant->getEmail()) {
                    $nb = $p;
                }
            }
            if ($form->isSubmitted() && $form->isValid()) {


                            if ($nb != null) {
                                return $this->redirectToRoute('quizpublic', ['quiz' => $quiz->getId(),'par' => $nb->getId()]);

                            } else {
                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($participant);
                                $entityManager->flush();

                                return $this->redirectToRoute('quizpublic', ['quiz' => $quiz->getId(),'par' => $participant->getId()]);

                            }

                }


            return $this->render('participant/new.html.twig', [
                'participant' => $participant,
                'form' => $form->createView(),
            ]);

        }


    /**
     * @Route("/{email}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{email}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant_index', [
                'email' => $participant->getEmail(),
            ]);
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{email}", name="participant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getEmail(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index');
    }
}
