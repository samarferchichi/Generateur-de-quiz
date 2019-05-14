<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home_page_index", methods={"GET"})
     */
    public function index(): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('/index.html.twig', [
        ]);
    }
}





