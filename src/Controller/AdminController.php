<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Parametre;
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
use phpDocumentor\Reflection\Types\Integer;
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
class AdminController extends EasyAdminController
{

    /**
     * @Route("/finduser", name="finduser")
     */
    public function finduser()
    {

        return $this->getUser()->getId();
    }





    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboardAction(ParticipantQuizRepository $participantQuizRepository)
    {
        $parquiz = $participantQuizRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $user = $this->getUser();

            $quiz = $em->getRepository('App:Quiz')->findAll();


        return $this->render('/dashboard.html.twig', array('quiz'=>$quiz, 'user'=>$user, 'parquiz'=>$parquiz));
    }



    /**
     * @Route("/{id}/{page}/creerquiz", name="creerquiz", methods={"GET","POST"})
     * @ParamConverter("page", class="App:Page")
     * @ParamConverter("quiz", class="App:Quiz")
     *
     *
     */
    public function creerquizAction(Quiz $quiz, Page $page,QuestionRepository $questionRepository,ParametreRepository $parametreRepository, ReponseRepository $reponseRepository ,QuizRepository $quizRepository, Request $request) : Response
    {


        $listquestion = $questionRepository->findBy(array('page' => $page->getId()));

        $listquiz = $quizRepository->findAll();


        $data_ordre = [''];
        foreach ($quiz->getPage() as $p){
            array_push($data_ordre, $p->getOrdre());
        }
        $pos = array_search($page->getOrdre(), $data_ordre);

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setQuiz($quiz);
            $entityManager->persist($page);
            $this->getDoctrine()->getManager()->flush();
        }

        $question = new Question();
        $formq = $this->createForm(QuestionType::class, $question);
        $formq->handleRequest($request);


        if ($formq->isSubmitted() && $formq->isValid()) {


            if  ($question->getTypeQuestion() == "Vrai/faux") {
                $entityManager = $this->getDoctrine()->getManager();

                $etat = $request->get('etat');
                if($etat == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }

                $dataetat = [''];
                foreach ($etat as $p) {
                    array_push($dataetat, $p);
                }

                $vf = $request->get('vf');
                $data = [''];
                foreach ($vf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data, $p);
                    }
                }

                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setEtatvf($dataetat[$i]);
                    $entityManager->persist($reponse);
                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Case à cocher") {
                $entityManager = $this->getDoctrine()->getManager();


                $etatcase = $request->get('case');

                if($etatcase == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }


                $caseetat = [''];
                foreach ($etatcase as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($caseetat, $p);
                    }
                }

                $case = $request->get('etatcase');
                $data = [''];
                foreach ($case as $p) {

                    array_push($data, $p);

                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($caseetat[$i]);
                    $reponse->setEtatcaseacocher($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Liste déroulante") {
                $entityManager = $this->getDoctrine()->getManager();

                $etatcase = $request->get('list');
                if($etatcase == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }

                $caseetat = [''];
                foreach ($etatcase as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($caseetat, $p);
                    }
                }

                $case = $request->get('etatlist');
                $data = [''];
                foreach ($case as $p) {
                    array_push($data, $p);
                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($caseetat[$i]);
                    $reponse->setEtatlist($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Calculée") {
                $entityManager = $this->getDoctrine()->getManager();


                $descriptionf = $request->get('descriptionF');
                if($descriptionf == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }

                $data_description = [''];
                foreach ($descriptionf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de description est vide !! il faut remplire touts les description !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_description, $p);
                    }
                }


                $formule = $request->get('formule');
                $data_formule = [''];
                foreach ($formule as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de formule est vide !! il faut remplire touts les formule !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_formule, $p);
                    }
                }

                $resultatf = $request->get('resultatF');
                $data_resultat = [''];
                foreach ($resultatf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de resultat est vide !! il faut remplire touts les resultat !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_resultat, $p);
                    }
                }

                for ($i = 1; $i < count($data_resultat); $i++) {
                    $reponse = new Reponse();
                    $reponse->setDescriptionformule($data_description[$i]);
                    $reponse->setQuestion($question);
                    $reponse->setFormule($data_formule[$i]);
                    $reponse->setResultatformule($data_resultat[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            }
            elseif ($question->getTypeQuestion() == 'Date') {

                $parametre = new Parametre();
                $entityManager = $this->getDoctrine()->getManager();
                $parametre->setFormText("date");
                $parametre->setQuestion($question);
                $entityManager->persist($parametre);

                $date = $request->get('date');

                if($date == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }


                $data = [''];
                foreach ($date as $p) {
                    if($p == null )
                    {
                        $this->addFlash('danger','Une champ Date est vide !! il faut remplire touts les dates !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    }else {
                        array_push($data, $p);
                    }
                }

                $data_desc = $request->get('desc');

                $datad = [''];
                foreach ($data_desc as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ description de date est vide !! il faut remplire touts les descriptions !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datad, $p);
                    }

                }

                for ($i = 1; $i < count($datad); $i++) {


                    $reponse = new Reponse();

                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setDescriptiondate($datad[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == 'Nombre') {


                $parametre = new Parametre();
                $entityManager = $this->getDoctrine()->getManager();
                $parametre->setFormText("number");
                $parametre->setQuestion($question);
                $parametre->setNbChiffre($request->get('nbChiffre'));
                $entityManager->persist($parametre);



                $number = $request->get('number');

                if($number == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                }

                $desnum = $request->get('desnum');

                $datanum = [''];
                foreach ($desnum as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ description est vide !! il faut remplire touts les description !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datanum, $p);
                    }
                }



                $datanumber = [''];
                foreach ($number as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ numero est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datanumber, $p);
                    }
                }

                $data = [''];
                foreach ($number as $p) {
                    array_push($data, $p);
                }


                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setDesnumber($datanum[$i]);

                    $reponse->setReponseValide($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            }
            $entityManager = $this->getDoctrine()->getManager();

            $question->setDescriptionQuestion($question->getDescriptionQuestion());
            $question->setPage($page);
            $question->setActif(true);
            $entityManager->persist($question);
            $this->getDoctrine()->getManager()->flush();


        return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

        }

        $listreponse = $reponseRepository->findAll();
        $listparametre = $parametreRepository->findAll();

        return $this->render('quiz/creerquiz.html.twig', [
                    'page' => $page,
                    'quiz' => $quiz,
                    'form' => $form->createView(),
                    'formq' => $formq->createView(),
                    'question' => $question,
                    'listquestion' => $listquestion,
                    'pos'   => $pos,
                    'listquiz'=>$listquiz,
                    'listparametre'=>$listparametre,
                    'listreponse'=>$listreponse,
                    'user' => $this->getUser()
                ]
            );
        }


    /**
     * @Route("/{quiz}/{page}/creatnextpage", name="creatNextPage", methods={"GET","POST"})
     */
    public function creatnextpage(Request $request, PageRepository $pageRepository, Quiz $quiz, Page $page): Response
    {
        $totalPages = count($quiz->getPage());
        $data_ordre = [''];
        foreach ($quiz->getPage() as $p){
            array_push($data_ordre, $p->getOrdre());
        }
        $pos = array_search($page->getOrdre(), $data_ordre);

        if($quiz->getNbPage() == $totalPages && $pos == $totalPages){
            return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(),'page' => $page->getId()]);
        }elseif ($quiz->getNbPage() > $totalPages && $pos == $totalPages){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);

            $page = new Page();
            $page->setQuiz($quiz);
            $page->setBgColor($page->getBgColor());
            $page->setColorTitrePage('');
            $page->setTitrePage('Vide');
            $ordre = $pageRepository->getMaxOrdre($quiz->getId());
            $page->setOrdre(($ordre[0]['maxOrdre'])+1);

            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('creerquiz',['id'=>$quiz->getId(),'page'=>$page->getId()] );
        }else{
            return $this->redirectToRoute('creerquiz',['id'=>$quiz->getId(),'page'=>$quiz->getPage()[$pos]->getId()] );
        }

    }


    /**
     * @Route("/{quiz}/{page}/precedentPage", name="precedentPage", methods={"GET","POST"})
     */
    public function precedentPage(Request $request, PageRepository $pageRepository, Quiz $quiz, Page $page): Response
    {

        $data_ordre = [''];
        foreach ($quiz->getPage() as $p){
            array_push($data_ordre, $p->getOrdre());
        }
        $pos = array_search($page->getOrdre(), $data_ordre) - 2 ;

        $pagePrec = $quiz->getPage()[$pos];

        if($pos >= 0)
            return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(),'page' => $pagePrec->getId()]);
        else
            return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(),'page' => $page->getId()]);

    }




    /**
     * @Route("/quiz/list", name="list_quiz", methods={"GET","POST"})
     */
    public function list_quiz(Request $request, QuizRepository $quizRepository, PageRepository $pageRepository): Response
    {
        $listquiz = $quizRepository->findAll();

        $user = $this->getUser();


        $listpage = $pageRepository->findAll();




        return $this->render('quiz/list.html.twig',[
               'listequiz' => $listquiz,
            'user' => $user,

        ]);
    }



    /**
     * @Route("/back/{quiz}", name="back", methods={"GET","POST"})
     */
    public function back( Quiz $quiz, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findAll();

       return $this->redirectToRoute('get_info_quiz',[
           'quiz'=>$quiz
       ]);
    }


    /**
     * @Route("/imprimerquiz/{quiz}", name="imprimerquiz", methods={"GET","POST"})
     */
    public function imprimerquiz( Quiz $quiz, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findAll();

        return $this->render('quiz/imprimerquiz.html.twig',[
            'quiz'=>$quiz,
            'listquiz'=>$listquiz
        ]);
    }


    /**
     * @Route("/showpagequestion/{page}", name="showpagequestion", methods={"GET","POST"})
     */
    public function showpagequestion( Page $page, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findAll();

        return $this->render('quiz/showpagequestion.html.twig',[
            'p'=>$page
        ]);
    }


    /**
     * @Route("/showq/{quiz}", name="showq", methods={"GET","POST"})
     */
    public function showqAction( Quiz $quiz, QuizRepository $quizRepository)
    {
        $listquiz = $quizRepository->findAll();

        return $this->render('quiz/showQuiz.html.twig',[
            'quiz'=>$quiz
        ]);
    }


    /**
     * @Route("/send/{quiz}", name="send", methods={"GET","POST"})
     */
    public function sendAction(Request $request, Quiz $quiz)
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('send', ['quiz' => $quiz->getId()])
        ])
            ->add('email', EmailType::class,[
                    'attr' => [
                        'class' => 'form-control'
                    ]
            ]

                )
            ->add('description', TextareaType::class,[
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $to = $form->getData()['email'];
            $des = $form->getData()['description'];

            $transport = (new \Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
                ->setUsername('samarferchichi61@gmail.com')
                ->setPassword('samarferchichi1234');

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);

            // Create a message
            $body = $this->renderView(
            // templates/emails/registration.html.twig
                'emails/send_quiz.html.twig', ['quiz' => $quiz, 'message' =>$des]);
            $message = (new \Swift_Message('Email Through Swift Mailer'))
                ->setFrom(['samarferchichi61@gmail.com' => 'Quiz'])
                ->setTo([$to])
                //    ->setCc(['RECEPIENT_2_EMAIL_ADDRESS'])
                //   ->setBcc(['RECEPIENT_3_EMAIL_ADDRESS'])
                ->setBody($body)
                ->setContentType('text/html')
            ;

            // Send the message
            $mailer->send($message);

            return $this->redirectToRoute('send', ['quiz' => $quiz->getId()]);
        }


        return $this->render('quiz/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/quiz/dupliqueAction/{quiz}", name="dupliqueAction", methods={"GET","POST"})
     */
        public function dupliqueAction(QuizRepository $quizRepository, Quiz $quiz)
    {
        $entityManager = $this->getDoctrine()->getManager();
$copie=0;

$quizs=$quizRepository->findAll();
        foreach($quizs as $d){
            $aux = explode($d->getTitre().'_Copie(', $quiz->getTitre());
            $copie=$copie+1;
        }

        $qui =new Quiz();
        if($quiz->getNbQuestion())
            $qui->setNbQuestion($quiz->getNbQuestion());
        if($quiz->getNbPage())
            $qui->setNbPage($quiz->getNbPage());
        if($quiz->getTerminer())
            $qui->setTerminer($quiz->getTerminer());
        if($quiz->getCategorie())
            $qui->setCategorie($quiz->getCategorie());

        if($quiz->getUser())
            $qui->setUser($quiz->getUser());
        $qui->setDatecreerquiz($quiz->getDatecreerquiz());

        if($quiz->getTitre())
            $qui->setTitre($quiz->getTitre()."_($copie)");
        if($quiz->getColorTitre())
            $qui->setColorTitre($quiz->getColorTitre());
        if($quiz->getBrochure())
            $qui->setBrochure($quiz->getBrochure());
        if($quiz->getDescription())
            $qui->setDescription($quiz->getDescription());
        if($quiz->getEntete())
            $qui->setEntete($quiz->getEntete());
        if($quiz->getPied())
            $qui->setPied($quiz->getPied());
        if($quiz->getFermerQuiz())
            $qui->setFermerQuiz($quiz->getFermerQuiz());
        if($quiz->getOuvrireQuiz())
            $qui->setOuvrireQuiz($quiz->getOuvrireQuiz());
        if($quiz->getGras())
            $qui->setGras($quiz->getGras());
        if($quiz->getItalique())
            $qui->setItalique($quiz->getItalique());
        if($quiz->getImprimePdf())
            $qui->setImprimePdf($quiz->getImprimePdf());
        if($quiz->getMelangeQuestion())
            $qui->setMelangeQuestion($quiz->getMelangeQuestion());
        if($quiz->getNbTentative())
            $qui->setNbTentative($quiz->getNbTentative());
        if($quiz->getMessageE())
            $qui->setMessageE($quiz->getMessageE());
        if($quiz->getMessageS())
            $qui->setMessageS($quiz->getMessageS());
        if($quiz->getModeCorrection())
            $qui->setModeCorrection($quiz->getModeCorrection());
        if($quiz->getSendMail())
            $qui->setSendMail($quiz->getSendMail());
        if($quiz->getTempsDispo())
            $qui->setTempsDispo($quiz->getTempsDispo());

        $qui->setTimequiz($quiz->getTimequiz());


        $entityManager->persist($qui);
        foreach ($quiz->getPage() as $par) {
        $newpage = new Page();
           if($par->getBgColor())
                $newpage->setBgColor($par->getBgColor());
            if($par->getOrdre())
                $newpage->setOrdre($par->getOrdre());
            if($par->getTitrePage())
                $newpage->setTitrePage($par->getTitrePage());
            if($par->getColorTitrePage())
                $newpage->setColorTitrePage($par->getColorTitrePage());
              $newpage->setQuiz($qui);
            $entityManager->persist($newpage);
            foreach ($par->getQuestion() as $quest) {
                $newquestion =new Question();
                $newquestion->setTextQuestion($quest->getTextQuestion());
                $newquestion->setTypeQuestion($quest->getTypeQuestion());
                if ($quest->getDescriptionQuestion())
                    $newquestion->setDescriptionQuestion($quest->getDescriptionQuestion());
                if ($quest->getInfoBulle())
                    $newquestion->setInfoBulle($quest->getInfoBulle());
                $newquestion->setActif($quest->getActif());
                $newquestion->setPage($newpage);
                $entityManager->persist($newquestion);

                foreach ($quest->getReponse() as $rep) {

                    $newrep = new Reponse();
                    if ($rep->getDescriptiondate())
                        $newrep->setDescriptiondate($rep->getDescriptiondate());
                    if ($rep->getDescriptionformule())
                        $newrep->setDescriptionformule($rep->getDescriptionformule());
                    if ($rep->getResultatformule())
                        $newrep->setResultatformule($rep->getResultatformule());
                    if ($rep->getFormule())
                        $newrep->setFormule($rep->getFormule());
                    if ($rep->getEtatlist())
                        $newrep->setEtatlist($rep->getEtatlist());
                    $newrep->setEtatcaseacocher($rep->getEtatcaseacocher());
                    if ($rep->getEtatcaseacocher())
                        $newrep->setEtatvf($rep->getEtatvf());
                    if ($rep->getDesnumber())
                        $newrep->setDesnumber($rep->getDesnumber());

                    if ($rep->getReponseValide())
                        $newrep->setReponseValide($rep->getReponseValide());
                    $newrep->setQuestion($newquestion);
                    $entityManager->persist($newrep);
                }
                }
        }
        $listquiz=$quizRepository->findAll();

        foreach ( $listquiz as $q){

            if ($q->getTitre() == $qui->getTitre()){
                $copie=$copie+1;
            }
        }

        $entityManager->flush();



        return $this->redirectToRoute('list_quiz',[
            'listequiz' => $listquiz,
            'copie' => $copie

        ]);
    }


    /**
     * @Route("/quiz/new", name="quiz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            $file = $quiz->getBrochure();

            if($file == null){
                $fileName= "null" ;
            }
            else {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('brochures_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                }
            }

           if($quiz->getFermerQuiz())
               $quiz->setFermerQuiz(new \DateTime($quiz->getFermerQuiz()));
           if ($quiz->getOuvrireQuiz())
               $quiz->setOuvrireQuiz(new \DateTime($quiz->getOuvrireQuiz()));

           $quiz->setDatecreerquiz(new \DateTime("now"));

            $quiz->setBrochure($fileName);

            $quiz->setUser($this->getUser());

            $entityManager->persist($quiz);


            $page = new Page();
            $page->setQuiz($quiz);
            $page->setBgColor('	');
            $page->setColorTitrePage('');
            $page->setTitrePage('Vide');
            $page->setOrdre(1);

            $entityManager->persist($page);


            $entityManager->flush();

            $this->addFlash('success','Votre Quiz est bien configuré');


            return $this->redirectToRoute('creerquiz',['id'=>$quiz->getId(),'page'=>$page->getId()] );
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("/getInfoQuiz/{quiz}", name="get_info_quiz", methods={"GET", "POST"})
     */
    public function getInfoQuiz(Quiz $quiz)
    {
        return $this->render('quiz/info_quiz.html.twig', [
            'quiz' => $quiz,
        ]);
    }



    /**
     * @Route("/infoPage/{quiz}/{quizactuel}/{pageactuel}", name="infoPage", methods={"GET", "POST"})
     */
    public function infoPage(Quiz $quiz ,Quiz $quizactuel, Page $pageactuel, Request $request)
    {

        return $this->render('quiz/importePage.html.twig', [
            'quiz' => $quiz,
            'quizactuel' => $quizactuel,
            'pageactuel' => $pageactuel
        ]);
    }



    /**
     * @Route("/infoQuestion/{page}/{quizactuel}/{pageactuel}", name="infoQuestion", methods={"GET", "POST"})
     */
    public function infoQuestion(Page $page,Quiz $quizactuel, Page $pageactuel, QuestionRepository $questionRepository, ParametreRepository $parametreRepository, ReponseRepository $reponseRepository, Request $request)
    {

        $listquestion = $questionRepository->findBy(array('page' => $page->getId()));



        return $this->render('quiz/importeQuestion.html.twig', [
            'page' => $page,
            'listquestion' => $questionRepository->findAll(),
            'listparametre' => $parametreRepository->findAll(),
            'listreponse' => $reponseRepository->findAll(),
            'question' => $listquestion,
            'quizactuel' =>$quizactuel->getId(),
            'pageactuel'=>$pageactuel->getId()


        ]);
    }




    /**
     * @Route("/infoPage2/{quiz}/{quizactuel}/{pageactuel}", name="infoPage2", methods={"GET", "POST"})
     */
    public function infoPage2(Quiz $quiz ,Quiz $quizactuel, Page $pageactuel, Request $request)
    {

        return $this->render('quiz/importePage2.html.twig', [
            'quiz' => $quiz,
            'quizactuel' => $quizactuel,
            'pageactuel' => $pageactuel
        ]);
    }



    /**
     * @Route("/infoQuestion2/{page}/{quizactuel}/{pageactuel}", name="infoQuestion2", methods={"GET", "POST"})
     */
    public function infoQuestion2(Page $page,Quiz $quizactuel, Page $pageactuel, QuestionRepository $questionRepository, ParametreRepository $parametreRepository, ReponseRepository $reponseRepository, Request $request)
    {

        $listquestion = $questionRepository->findBy(array('page' => $page->getId()));



        return $this->render('quiz/importeQuestion2.html.twig', [
            'page' => $page,
            'listquestion' => $questionRepository->findAll(),
            'listparametre' => $parametreRepository->findAll(),
            'listreponse' => $reponseRepository->findAll(),
            'question' => $listquestion,
            'quizactuel' =>$quizactuel->getId(),
            'pageactuel'=>$pageactuel->getId()


        ]);
    }





    /**
     * @Route("/newQuestion/{quiz}/{page}", name="newQuestion", methods={"GET", "POST"})
     */
    public function newQuestion(Quiz $quiz, Page $page,QuestionRepository $questionRepository,ParametreRepository $parametreRepository, ReponseRepository $reponseRepository ,QuizRepository $quizRepository, Request $request) : Response
    {
        $listquestion = $questionRepository->findBy(array('page' => $page->getId()));

        $listquiz = $quizRepository->findAll();


        $data_ordre = [''];
        foreach ($quiz->getPage() as $p){
            array_push($data_ordre, $p->getOrdre());
        }
        $pos = array_search($page->getOrdre(), $data_ordre);

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setQuiz($quiz);
            $entityManager->persist($page);
            $this->getDoctrine()->getManager()->flush();
        }

        $question = new Question();
        $formq = $this->createForm(QuestionType::class, $question, [
            'action' => $this->generateUrl('newQuestion', [
                'quiz' => $quiz->getId(),
                'page' => $page->getId(),
            ])
        ]);
        $formq->handleRequest($request);


        if ($formq->isSubmitted() && $formq->isValid()) {


            if  ($question->getTypeQuestion() == "Vrai/faux") {
                $entityManager = $this->getDoctrine()->getManager();

                $etat = $request->get('etat');
                if($etat == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz' => $quiz->getId(), 'page' => $page->getId()]);

                }

                $dataetat = [''];
                foreach ($etat as $p) {
                    array_push($dataetat, $p);
                }

                $vf = $request->get('vf');
                $data = [''];
                foreach ($vf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data, $p);
                    }
                }

                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setEtatvf($dataetat[$i]);
                    $entityManager->persist($reponse);
                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Case à cocher") {
                $entityManager = $this->getDoctrine()->getManager();


                $etatcase = $request->get('case');

                if($etatcase == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                }


                $caseetat = [''];
                foreach ($etatcase as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($caseetat, $p);
                    }
                }

                $case = $request->get('etatcase');
                $data = [''];
                foreach ($case as $p) {

                    array_push($data, $p);

                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($caseetat[$i]);
                    $reponse->setEtatcaseacocher($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Liste déroulante") {
                $entityManager = $this->getDoctrine()->getManager();

                $etatcase = $request->get('list');
                if($etatcase == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                }

                $caseetat = [''];
                foreach ($etatcase as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($caseetat, $p);
                    }
                }

                $case = $request->get('etatlist');
                $data = [''];
                foreach ($case as $p) {
                    array_push($data, $p);
                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($caseetat[$i]);
                    $reponse->setEtatlist($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == "Calculée") {
                $entityManager = $this->getDoctrine()->getManager();


                $descriptionf = $request->get('descriptionF');
                if($descriptionf == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                }

                $data_description = [''];
                foreach ($descriptionf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de description est vide !! il faut remplire touts les description !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_description, $p);
                    }
                }


                $formule = $request->get('formule');
                $data_formule = [''];
                foreach ($formule as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de formule est vide !! il faut remplire touts les formule !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_formule, $p);
                    }
                }

                $resultatf = $request->get('resultatF');
                $data_resultat = [''];
                foreach ($resultatf as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ de resultat est vide !! il faut remplire touts les resultat !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($data_resultat, $p);
                    }
                }

                for ($i = 1; $i < count($data_resultat); $i++) {
                    $reponse = new Reponse();
                    $reponse->setDescriptionformule($data_description[$i]);
                    $reponse->setQuestion($question);
                    $reponse->setFormule($data_formule[$i]);
                    $reponse->setResultatformule($data_resultat[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            }
            elseif ($question->getTypeQuestion() == 'Date') {

                $parametre = new Parametre();
                $entityManager = $this->getDoctrine()->getManager();
                $parametre->setFormText("date");
                $parametre->setQuestion($question);
                $entityManager->persist($parametre);

                $date = $request->get('date');

                if($date == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                }


                $data = [''];
                foreach ($date as $p) {
                    if($p == null )
                    {
                        $this->addFlash('danger','Une champ Date est vide !! il faut remplire touts les dates !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    }else {
                        array_push($data, $p);
                    }
                }

                $data_desc = $request->get('desc');

                $datad = [''];
                foreach ($data_desc as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ description de date est vide !! il faut remplire touts les descriptions !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datad, $p);
                    }

                }

                for ($i = 1; $i < count($datad); $i++) {


                    $reponse = new Reponse();

                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setDescriptiondate($datad[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            } elseif ($question->getTypeQuestion() == 'Nombre') {


                $parametre = new Parametre();
                $entityManager = $this->getDoctrine()->getManager();
                $parametre->setFormText("number");
                $parametre->setQuestion($question);
                $parametre->setNbChiffre($request->get('nbChiffre'));
                $entityManager->persist($parametre);



                $number = $request->get('number');

                if($number == null )
                {
                    $this->addFlash('danger','Il faut ajouter une reponse');

                    return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                }

                $desnum = $request->get('desnum');

                $datanum = [''];
                foreach ($desnum as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ description est vide !! il faut remplire touts les description !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datanum, $p);
                    }
                }



                $datanumber = [''];
                foreach ($number as $p) {
                    if ($p == null) {
                        $this->addFlash('danger', 'Une champ numero est vide !! il faut remplire touts les champs !');

                        return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

                    } else {
                        array_push($datanumber, $p);
                    }
                }

                $data = [''];
                foreach ($number as $p) {
                    array_push($data, $p);
                }


                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setDesnumber($datanum[$i]);

                    $reponse->setReponseValide($data[$i]);
                    $entityManager->persist($reponse);

                }
                $this->addFlash('success','Question est bien ajouter a la page');

            }
            $entityManager = $this->getDoctrine()->getManager();

            $question->setDescriptionQuestion($question->getDescriptionQuestion());
            $question->setPage($page);
            $question->setActif(true);
            $entityManager->persist($question);
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('modifier_page', ['quiz'  => $quiz->getId(), 'page' => $page->getId()]);

        }

        return $this->render('quiz/newQuestion.html.twig', [
                'page' => $page,
                'quiz' => $quiz,
                'form' => $form->createView(),
                'formq' => $formq->createView(),
                'question' => $question,
                'listquestion' => $listquestion,
                'pos'   => $pos,
                'listquiz'=>$listquiz,
                'user' => $this->getUser()


            ]
        );
    }



    /**
     * @Route("/modifier_page/{quiz}/{page}", name="modifier_page", methods={"GET", "POST"})
     */
    public function modifier_page(Page $page, Quiz $quiz, Request $request)
    {
        $question=$page->getQuestion();

        $form = $this->createForm(PageType::class, $page);
        if ($page->getTitrePage())
            $page->setTitrePage($page->getTitrePage());

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_quiz');
        }

        return $this->render('quiz/modifierPage.html.twig', [
            'quiz' => $quiz,
            'page' => $page,
            'question'=>$question,
            'form' => $form->createView(),
        ]);

    }







    /**
     * @Route("/quiz_index", name="quiz_index", methods={"GET"})
     */
    public function indexquiz(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="quiz_edit", methods={"GET","POST"})
     */
    public function editq(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quiz_index', [
                'id' => $quiz->getId(),
            ]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/{quiz}/edit", name="page_edit", methods={"GET","POST"})
     * @ParamConverter("quiz", class="App:Quiz")
     */
    public function edit(Request $request, Page $page, Quiz $quiz): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setQuiz($quiz);
            $entityManager->persist($page);
            $entityManager->flush();
        }
        return $this->render('page/edit.html.twig', [
            'quiz'=>$quiz,
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/dashboard", name="admin", methods={"GET" , "POST"})
     */
    public function admin()
    {
        return $this->render('admin');
    }




    /**
     * @Route("/{id}/{page}/ajout", name="page_ajout", methods={"GET","POST"})
     * @ParamConverter("quiz", class="App:Quiz")
     */
    public function ajoutPage(Request $request, Page $page, Quiz $quiz): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $quiz->setNbPage($quiz->getNbpage()+1);
            $entityManager->flush();

            return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);
    }



    /**
     * @Route("/add-page-to-quiz/{quiz}", name="add_page_to_quiz", methods={"GET","POST"})
     * @ParamConverter("quiz", class="App:Quiz")
     */
    public function addPageToQuiz(Request $request, Quiz $quiz, PageRepository $pageRepository): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        if($quiz->getNbPage() > count($quiz->getPage())){
            $page = new Page();
            $page->setQuiz($quiz);
            $page->setBgColor('');
            $page->setColorTitrePage('');
            $page->setTitrePage('Vide');
            $ordre = $pageRepository->getMaxOrdre($quiz->getId());
            $page->setOrdre(($ordre[0]['maxOrdre'])+1);

            $entityManager->persist($page);
            $entityManager->flush();
        }

        return new JsonResponse(true);

    }


    /**
     * @Route("/{id}", name="quiz_delete", methods={"DELETE"})
     * @ParamConverter("quiz", class="App:Quiz")
     */
    public function deletequiz (Request $request, Quiz $quiz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }



    /**
     * @Route("/{page}/{quiz}/delete2", name="page_supprimer2", methods={"GET","POST", "DELETE"})
     */
    public function deletepage2(Request $request, Page $page, Quiz $quiz): Response
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('page_supprimer2', ['page' => $page->getId(), 'quiz'=>$quiz->getId()])
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

                return $this->redirectToRoute('list_quiz', [
                    'quiz' => $p->getQuiz()->getId(),
                    'page' => $p->getId()
                ]);
            }else{
                $page->getQuiz()->setNbPage($page->getQuiz()->getNbPage()-1);
                $em->remove($page->getQuiz()->getPage()[$pos - 1]);
                $em->flush();

                $p = $page->getQuiz()->getPage()[$pos - 2];

                return $this->redirectToRoute('list_quiz');

            }

        }

        return $this->render('page/_delete_form.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/quiz/form-update/max-page/{quiz}", name="form_update_max_page", methods={"POST"})
     */
    public function formUpdateMaxPage(Request $request, Quiz $quiz): Response
    {
        $form = $this->createFormBuilder()
            ->add('max', IntegerType::class, [
                'label' => 'Nombre maximum des pages ',
                'attr' => [
                    'value' => $quiz->getNbPage(),
                    'min' => $quiz->getNbPage(),
                    'class' => 'form-control'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        return $this->render('quiz/update_max_page.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/quiz/update/{quiz}/max-page/{max}", name="update_max_page", methods={"POST"})
     */
    public function updateMaxPage(Request $request, Quiz $quiz, $max): Response
    {
        $em = $this->getDoctrine()->getManager();
        $quiz->setNbPage($max);
        $em->flush();
        return new JsonResponse(true);
    }






}
