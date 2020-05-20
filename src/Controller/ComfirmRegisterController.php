<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ComfirmRegisterController extends AbstractController
{
    /**
     * @Route("/comfirm/register/{mail}/{key}", name="comfirm_register")
     */
    public function index(ManagerRegistry $doctrine ,$mail = null , $key = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $username = urldecode($mail);
        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$username]);
        if($user->getConfirmKey() == $key){
            $user->setComfirmed(1);
            $user->setDateInscription(new \DateTime());
            $entityManager->flush();
            $this->addFlash('confirm', 'Votre compte "' . $user->getUsername() . '" a correctement été activé !');

            return $this->redirectToRoute("Index.index");
            //return $this->render('index/index.html.twig');
        }

        return $this->render('comfirm_register/index.html.twig', [
            'controller_name' => 'ComfirmRegisterController',
        ]);
    }
}
