<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Signalement;
use App\Form\SignalementType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class SignalementController extends AbstractController
{
    /**
     * @Route("/signalement/{idpubli}", name="User.signalement")
     */
    public function index($idpubli = null, ManagerRegistry $doctrine, Request $request, Environment $twig)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $publication = $doctrine->getRepository(Publication::class)->findOneBy(['id'=>$idpubli]);
        if(!$publication){
            return $this->redirectToRoute("Index.index");
        }
        $publication2 = $doctrine->getRepository(Signalement::class)->findOneBy(array('publication' => $idpubli, 'auteur' => $this->getUser(), 'etat' => array('A traiter', 'En cours')));
        if($publication2){
            $this->addFlash('success', 'Publication déjà signalée.');
            return $this->redirectToRoute("Index.index");
        }

        $Signalement = new Signalement();
        $form = $this->createForm(SignalementType::class, $Signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $Signalement->setAuteur($this->getUser());
            $Signalement->setDateSignalement(new \DateTime());
            $Signalement->setEtat("A traiter");
            $Signalement->setPublication($publication);


            $entityManager->persist($Signalement);
            $entityManager->flush();
            $this->addFlash('success', 'Signalement envoyé.');

            return $this->redirectToRoute("Index.index");

        }
        return new Response($twig->render('signalement/index.html.twig',['form'=>$form->createView(), 'user' => $this->getUser()]));

    }
}
