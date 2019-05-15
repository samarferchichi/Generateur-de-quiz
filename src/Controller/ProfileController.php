<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
    }

    public function editPhotoAction(Request $request) {
        $user = $this->getUser();
        $form = $this->createFormBuilder();

        $form = $form->getForm()
                     ->add('image', FileType::class);

        $form->handleRequest($request);

        if($request->isMethod('POST')){

            $file = $form->getData()['image'];
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );
            $user->setAvatar($fileName);

            $this->userManager->updateUser($user);
            $this->addFlash('success', 'Photo changée avec succès.');

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('@FOSUser/Profile/photo.html.twig', array(
            'form' => $form->createView(),
            'profile' => $user,
        ));
    }
}
