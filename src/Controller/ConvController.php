<?php


namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Publication;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConvController extends AbstractController
{
    /**
     * @Route("/messages", name="Conv.conv")
     */
    public function index(ManagerRegistry $doctrine)
    {
        //$conv = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser(), 'interlocuteur' => 'admin'));
        $conv = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser()));

        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')) {
            return $this->redirectToRoute("User.setusername");
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            return $this->render('conv/index.html.twig', [
                'user' => $this->getUser(),
                'conv' => $conv
            ]);
        }
        else {
            return $this->render('index/index.html.twig', [
                'controller_name' => 'IndexController',
            ]);
        }

    }
}
