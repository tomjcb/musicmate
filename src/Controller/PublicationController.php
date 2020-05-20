<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Publication;
use App\Entity\User;
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

class PublicationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/publication", name="User.publication")
     */
    public function index(ManagerRegistry $doctrine, Request $request, Environment $twig, UserPasswordEncoderInterface $encoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Publication = new Publication();
        $form = $this->createForm(PublicationType::class, $Publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $Publication->setAuteur($this->getUser());

                $new_acti = new Activite();
                $new_acti->setAuteur($this->getUser());
                $new_acti->setType("Publication");
                $new_acti->setAction("Ajout");
                $new_acti->setDateActivite(new \DateTime());


                $entityManager->persist($Publication);
                $entityManager->persist($new_acti);
                $entityManager->flush();
                //$this->addFlash('success', $form['prenom']->getData().' ' . $form['nom']->getData() . ' votre inscription est un succès. Regardez vos mails pour activer votre compte');

            return $this->redirectToRoute("Index.index");
        }
        return new Response($twig->render('publication/index.html.twig',['form'=>$form->createView(), 'user' => $this->getUser()]));
        //return $this->render('register/index.html.twig', ['controller_name' => 'IndexController',]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/publication/delete/{authorid}/{id}", name="User.deletePublication")
     */
    public function deletePubli(ManagerRegistry $doctrine, $id = null, $authorid = null){
        if($authorid != $this->getUser()->getId() ){
            $this->addFlash('danger', 'Accès interdit');
            return $this->redirectToRoute("Index.index");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $publi = $doctrine->getRepository(Publication::class)->findOneBy(['id'=>$id]);

        $new_acti = new Activite();
        $new_acti->setAuteur($this->getUser());
        $new_acti->setType("Publication");
        $new_acti->setAction("Suppression");
        $new_acti->setDateActivite(new \DateTime());

        $entityManager->persist($new_acti);

        $entityManager->remove($publi);
        $entityManager->flush();

        return $this->redirectToRoute("Index.index");
    }
}
