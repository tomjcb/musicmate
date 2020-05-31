<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Ban;
use App\Entity\Dislike;
use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\BanType;
use App\Form\PublicationType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class BanController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/ban/{iduser}", name="Admin.ban")
     */
    public function index(ManagerRegistry $doctrine, Request $request, Environment $twig, $iduser = null)
    {
        $userbanned = $doctrine->getRepository(User::class)->findOneBy(['id'=>$iduser]);
        if($userbanned){
            $entityManager = $this->getDoctrine()->getManager();
            $Ban = new Ban();
            $form = $this->createForm(BanType::class, $Ban);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $Ban->setUserbanned($userbanned);
                $Ban->setStartBan(new \DateTime());

                $entityManager->persist($Ban);
                $entityManager->flush();
                //$this->addFlash('success', $form['prenom']->getData().' ' . $form['nom']->getData() . ' votre inscription est un succès. Regardez vos mails pour activer votre compte');

                return $this->redirectToRoute("Admin.index");
            }
            return new Response($twig->render('admin/banUser.html.twig', ['form' => $form->createView(), 'user' => $this->getUser(), 'userbanned' => $userbanned]));
            //return $this->render('register/index.html.twig', ['controller_name' => 'IndexController',]);
        }
        return $this->redirectToRoute("Admin.index");

    }
}
