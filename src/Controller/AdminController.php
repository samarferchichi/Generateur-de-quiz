<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Parametre;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\PageType;
use App\Form\ParametreType;
use App\Form\QuestionType;
use App\Repository\PageRepository;
use App\Repository\ParametreRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Ecommerce\EcommerceBundle\Form\RechercheType;
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
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboardAction()
    {
        $em=$this->getDoctrine()->getManager();

            $quiz = $em->getRepository('App:Quiz')->findAll();

        return $this->render('/dashboard.html.twig', array('quiz'=>$quiz));
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


            if ($question->getTypeQuestion() == "Réponse courte") {
                $desnum=$request->get('desnum');

                $selecttype = $request->get('selecttype');
                if ($selecttype == 'texte') {
                    $nbcaractere = $request->get('nbcaractere');


                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();

                    $parametre->setFormText("texte");
                    $parametre->setQuestion($question);
                    $parametre->setNbCaractere($nbcaractere);
                    $entityManager->persist($parametre);

                    $reptext = $request->get('test');
                    $data = [''];
                    foreach ($reptext as $p) {
                        array_push($data, $p);
                    }

                    $datatext = [''];
                    foreach ($data as $p) {
                        array_push($datatext, $p);
                    }
                    for ($i = 1; $i < count($data); $i++) {
                        $reponse = new Reponse();
                        $reponse->setQuestion($question);
                        $reponse->setReponseValide($data[$i]);
                        $entityManager->persist($reponse);
                    }

                } elseif ($selecttype == 'date') {

                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();
                    $parametre->setFormText("date");
                    $parametre->setQuestion($question);
                    $entityManager->persist($parametre);

                    $date = $request->get('date');
                    $data = [''];
                    foreach ($date as $p) {
                        array_push($data, $p);
                    }

                    $data_desc = $request->get('desc');
                    $datad = [''];
                    foreach ($data_desc as $p) {
                        array_push($datad, $p);
                    }


                    for ($i = 1; $i < count($datad); $i++) {

                        $reponse = new Reponse();

                        $reponse->setQuestion($question);
                        $reponse->setDescriptiondate($datad[$i]);
                        $entityManager->persist($reponse);

                    }
                } elseif ($selecttype == 'number') {
                    $datanum = [''];
                    foreach ($desnum as $p) {
                        array_push($datanum, $p);
                    }

                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();
                    $parametre->setFormText("number");
                    $parametre->setQuestion($question);
                    $parametre->setNbChiffre($request->get('nbChiffre'));
                    $entityManager->persist($parametre);
                    $number = $request->get('number');
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

                }

            } elseif ($question->getTypeQuestion() == "Vrai/faux") {
                $entityManager = $this->getDoctrine()->getManager();
                $etat = $request->get('etat');
                $dataetat = [''];
                foreach ($etat as $p) {
                    array_push($dataetat, $p);
                }
                $vf = $request->get('vf');
                $data = [''];
                foreach ($vf as $p) {
                    array_push($data, $p);
                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setEtatvf($dataetat[$i]);
                    $entityManager->persist($reponse);
                }

            } elseif ($question->getTypeQuestion() == "Case à cocher") {
                $entityManager = $this->getDoctrine()->getManager();
                $etatcase = $request->get('case');
                $caseetat = [''];
                foreach ($etatcase as $p) {
                    array_push($caseetat, $p);
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
            } elseif ($question->getTypeQuestion() == "Liste déroulante") {
                $entityManager = $this->getDoctrine()->getManager();
                $etatcase = $request->get('list');
                $caseetat = [''];
                foreach ($etatcase as $p) {
                    array_push($caseetat, $p);
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
                    $reponse->setEtatcaseacocher($data[$i]);
                    $entityManager->persist($reponse);

                }

            } elseif ($question->getTypeQuestion() == "Calculée") {
                $entityManager = $this->getDoctrine()->getManager();
                $descriptionf = $request->get('descriptionF');
                $data_description = [''];
                foreach ($descriptionf as $p) {
                    array_push($data_description, $p);
                }


                $formule = $request->get('formule');
                $data_formule = [''];
                foreach ($formule as $p) {
                    array_push($data_formule, $p);
                }

                $resultatf = $request->get('resultatF');
                $data_resultat = [''];
                foreach ($resultatf as $p) {
                    array_push($data_resultat, $p);
                }

                for ($i = 1; $i < count($data_resultat); $i++) {
                    $reponse = new Reponse();
                    $reponse->setDescriptionformule($data_description[$i]);
                    $reponse->setQuestion($question);
                    $reponse->setFormule($data_formule[$i]);
                    $reponse->setResultatformule($data_resultat[$i]);
                    $entityManager->persist($reponse);

                }

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
                    'listreponse'=>$listreponse
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
            $page->setBgColor('');
            $page->setColorTitrePage('');
            $page->setTitrePage('');
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

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $listpage = $pageRepository->findAll();

        $performanceDatas = [];
        foreach ($listpage as $key => $p) {
            $performanceDatas[$key] = ['idQuiz'=>$p->getQuiz()->getId(), 'idPage'=>$p->getId(), 'titrePage'=>$p->getTitrePage()];
        }
        $reponse = new JsonResponse();
        $reponse->setData(array('page' => $performanceDatas));


        return $this->render('quiz/list.html.twig',[
               'listequiz' => $listquiz,
            'listepage'=>$reponse,

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
     * @Route("/send", name="send", methods={"GET","POST"})
     */
    public function sendAction()
    {

        return $this->render('quiz/send.html.twig');
    }


    /**
     * @Route("/quiz/dupliqueAction/{quiz}", name="dupliqueAction", methods={"GET","POST"})
     */
    public function dupliqueAction(QuizRepository $quizRepository, Quiz $quiz)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $qui =new Quiz();
        if($quiz->getNbQuestion())
            $qui->setNbQuestion($quiz->getNbQuestion());
        if($quiz->getNbPage())
            $qui->setNbPage($quiz->getNbPage());


        $nbquiz=$quizRepository->findAll();
        $listquiz=$quizRepository->findAll();

        $nb=0;

        foreach ($nbquiz as $p)
        {
            if ($p->getTitre() == $quiz->getTitre() ) {

                $nb=$nb+1;
                    }

        }

        if($quiz->getTitre())
            $copie = "(Copie".$nb.")";

            $qui->setTitre($quiz->getTitre().$copie);
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
        if($quiz->getModeScore())
            $qui->setModeScore($quiz->getModeScore());
        if($quiz->getSendMail())
            $qui->setSendMail($quiz->getSendMail());
        if($quiz->getTempsDispo())
            $qui->setTempsDispo($quiz->getTempsDispo());
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
                foreach ($quest->getParametre() as $param) {
                    $parametre = new Parametre();
                    if($param->getNbCaractere())
                        $parametre->setNbCaractere($param->getNbCaractere());
                    if($param->getNbChiffre())
                        $parametre->setNbChiffre($param->getNbChiffre());
                    if ($param->getFormText())
                        $parametre->setFormText($param->getFormText());
                    $parametre->setQuestion($newquestion);
                    $entityManager->persist($parametre);

                }
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
                    if ($rep->getReponseValide())
                        $newrep->setReponseValide($rep->getReponseValide());
                    $newrep->setQuestion($newquestion);
                    $entityManager->persist($newrep);
                }
                }
        }
        $entityManager->flush();

        return $this->redirectToRoute('list_quiz',[
            'listequiz' => $listquiz,

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

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $quiz->getBrochure();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('brochures_directory'),
                    $fileName
                );
            } catch (FileException $e) {
            }


            $quiz->setBrochure($fileName);


            $entityManager->persist($quiz);


            $page = new Page();
            $page->setQuiz($quiz);
            $page->setBgColor('');
            $page->setColorTitrePage('');
            $page->setTitrePage('');
            $page->setOrdre(1);

            $entityManager->persist($page);
            $entityManager->flush();

            $this->addFlash('good','Votre Quiz est bien configuré');


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
        $formq = $this->createForm(QuestionType::class, $question);
        $formq->handleRequest($request);


        if ($formq->isSubmitted() && $formq->isValid()) {


            if ($question->getTypeQuestion() == "Réponse courte") {
                $des=$request->get('destext');
                $desnum=$request->get('desnum');

                $selecttype = $request->get('selecttype');
                if ($selecttype == 'texte') {
                    $nbcaractere = $request->get('nbcaractere');
                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();
                    $parametre->setFormText("texte");
                    $parametre->setQuestion($question);
                    $parametre->setNbCaractere($nbcaractere);
                    $entityManager->persist($parametre);

                    $reptext = $request->get('test');
                    $data = [''];
                    foreach ($reptext as $p) {
                        array_push($data, $p);
                    }

                    $datatext = [''];
                    foreach ($des as $p) {
                        array_push($datatext, $p);
                    }
                    for ($i = 1; $i < count($data); $i++) {
                        $reponse = new Reponse();
                        $reponse->setQuestion($question);
                        $reponse->setReponseValide($data[$i]);
                        $entityManager->persist($reponse);
                    }

                } elseif ($selecttype == 'date') {

                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();
                    $parametre->setFormText("date");
                    $parametre->setQuestion($question);
                    $entityManager->persist($parametre);

                    $date = $request->get('date');
                    $data = [''];
                    foreach ($date as $p) {
                        array_push($data, $p);
                    }

                    $data_desc = $request->get('desc');
                    $datad = [''];
                    foreach ($data_desc as $p) {
                        array_push($datad, $p);
                    }


                    for ($i = 1; $i < count($datad); $i++) {

                        $reponse = new Reponse();

                        $reponse->setQuestion($question);
                        $reponse->setDescriptiondate($datad[$i]);
                        $entityManager->persist($reponse);

                    }
                } elseif ($selecttype == 'number') {
                    $datanum = [''];
                    foreach ($desnum as $p) {
                        array_push($datanum, $p);
                    }

                    $parametre = new Parametre();
                    $entityManager = $this->getDoctrine()->getManager();
                    $parametre->setFormText("number");
                    $parametre->setQuestion($question);
                    $parametre->setNbChiffre($request->get('nbChiffre'));
                    $entityManager->persist($parametre);
                    $number = $request->get('number');
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

                }

            } elseif ($question->getTypeQuestion() == "Vrai/faux") {
                $entityManager = $this->getDoctrine()->getManager();
                $etat = $request->get('etat');
                $dataetat = [''];
                foreach ($etat as $p) {
                    array_push($dataetat, $p);
                }
                $vf = $request->get('vf');
                $data = [''];
                foreach ($vf as $p) {
                    array_push($data, $p);
                }
                for ($i = 1; $i < count($data); $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponseValide($data[$i]);

                    $reponse->setEtatvf($dataetat[$i]);
                    $entityManager->persist($reponse);
                }

            } elseif ($question->getTypeQuestion() == "Case à cocher") {
                $entityManager = $this->getDoctrine()->getManager();
                $etatcase = $request->get('case');
                $caseetat = [''];
                foreach ($etatcase as $p) {
                    array_push($caseetat, $p);
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
            } elseif ($question->getTypeQuestion() == "Liste déroulante") {
                $entityManager = $this->getDoctrine()->getManager();
                $etatcase = $request->get('list');
                $caseetat = [''];
                foreach ($etatcase as $p) {
                    array_push($caseetat, $p);
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
                    $reponse->setEtatcaseacocher($data[$i]);
                    $entityManager->persist($reponse);

                }

            } elseif ($question->getTypeQuestion() == "Calculée") {
                $entityManager = $this->getDoctrine()->getManager();
                $descriptionf = $request->get('descriptionF');
                $data_description = [''];
                foreach ($descriptionf as $p) {
                    array_push($data_description, $p);
                }


                $formule = $request->get('formule');
                $data_formule = [''];
                foreach ($formule as $p) {
                    array_push($data_formule, $p);
                }

                $resultatf = $request->get('resultatF');
                $data_resultat = [''];
                foreach ($resultatf as $p) {
                    array_push($data_resultat, $p);
                }

                for ($i = 1; $i < count($data_resultat); $i++) {
                    $reponse = new Reponse();
                    $reponse->setDescriptionformule($data_description[$i]);
                    $reponse->setQuestion($question);
                    $reponse->setFormule($data_formule[$i]);
                    $reponse->setResultatformule($data_resultat[$i]);
                    $entityManager->persist($reponse);

                }

            }

            $entityManager = $this->getDoctrine()->getManager();
            $question->setPage($page);
            $question->setActif(true);


            $entityManager->persist($question);

            $this->getDoctrine()->getManager()->flush();



            return $this->redirectToRoute('creerquiz', ['id' => $quiz->getId(), 'page' => $page->getId()]);

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
     * @Route("/{id}/{page}/addpage", name="addpage", methods={"GET","POST"})
     * @ParamConverter("quiz", class="App:Quiz")
     */
    public function addpage(Request $request, Quiz $quiz, Page $page , PageRepository $pageRepository): Response
    {

        $entityManager = $this->getDoctrine()->getManager();


        $quiz->setNbPage($quiz->getNbpage()+1);
        $entityManager->persist($quiz);


        $totalPages = count($quiz->getPage());
        $data_ordre = [''];
        foreach ($quiz->getPage() as $p){
            array_push($data_ordre, $p->getOrdre());
        }
        $pos = array_search($page->getOrdre(), $data_ordre);

        if($quiz->getNbPage() == $totalPages && $pos == $totalPages){
            return $this->redirectToRoute('list_quiz', ['id' => $quiz->getId(),'page' => $page->getId()]);
        }elseif ($quiz->getNbPage() > $totalPages && $pos == $totalPages){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);

            $page = new Page();
            $page->setQuiz($quiz);
            $page->setBgColor('');
            $page->setColorTitrePage('');
            $page->setTitrePage('');
            $ordre = $pageRepository->getMaxOrdre($quiz->getId());
            $page->setOrdre(($ordre[0]['maxOrdre'])+1);

            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('list_quiz',['id'=>$quiz->getId(),'page'=>$page->getId()] );
        }else{
            return $this->redirectToRoute('list_quiz',['id'=>$quiz->getId(),'page'=>$quiz->getPage()[$pos]->getId()] );
        }

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









}
