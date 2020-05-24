<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\User;
use App\Form\GoogleUserType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ProfileController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/profil/{userName}", name="User.profil")
     */
    public function index(ManagerRegistry $doctrine, $userName = null)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$userName]);
        if($this->getUser()->getUsername() == $userName){
            return $this->render('profile/index.html.twig', [
                'user' => $user,
                'profileuser' => $this->getUser(),
                'currentUserProfile' => 'yes'
            ]);
        }
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
            'profileuser' => $user
        ]);
    }

    /**
     * @IsGranted("ROLE_NEEDUSERNAME")
     * @Route("/googleuser/username", name="User.setusername")
     */
    public function SetGoogleUsername(ManagerRegistry $doctrine, Request $request, Environment $twig){
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $User = $this->getUser();
        if($User->getGoogleId() == null){
            $social="Facebook";
        }
        else{
            $social="Google";
        }
        $form = $this->createForm(GoogleUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $User->setRoles('ROLE_USER');
                $User->setUsername($form['_username']->getData());

                $entityManager->persist($User);
                $entityManager->flush();

                if($social == "Google"){
                    return $this->redirectToRoute("connect_google_start");
                }
                else{
                    return $this->redirectToRoute("connect_facebook_start");
                }


        }
        return new Response($twig->render('socialuser/index.html.twig',['form'=>$form->createView(), 'user' => $this->getUser(), 'social' => $social]));
    }
}
