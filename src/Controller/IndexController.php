<?php

namespace App\Controller;

use App\Entity\Publication;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="Index.index")
     */
    public function index(ManagerRegistry $doctrine)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute("Admin.index");
        }

        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $publication = $doctrine->getRepository(Publication::class)->findBy(array(), array('id' => 'DESC'));

            if($publication){
                return $this->render('registered/index.html.twig', [
                    'user' => $this->getUser(),
                    'publications' => $publication
                ]);
            }

            return $this->render('registered/index.html.twig', [
                'user' => $this->getUser()
            ]);
        }


        else{
            return $this->render('index/index.html.twig', [
                'controller_name' => 'IndexController',
            ]);
        }

    }
}
