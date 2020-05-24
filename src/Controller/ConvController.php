<?php


namespace App\Controller;

use App\Entity\Publication;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConvController extends AbstractController
{
    /**
     * @Route("/conv", name="Conv.conv")
     */
    public function index(ManagerRegistry $doctrine)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')) {
            return $this->redirectToRoute("User.setusername");
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            return $this->render('conv/index.html.twig', [
                'user' => $this->getUser()
            ]);
        }
        else {
            return $this->render('index/index.html.twig', [
                'controller_name' => 'IndexController',
            ]);
        }

    }
}
