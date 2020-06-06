<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Commentaire;
use App\Entity\Dislike;
use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\PublicationType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' =>$this->getUser()->getUsername()]);
        $entityManager = $this->getDoctrine()->getManager();
        $session = new Session(
            'c1d54f3ca5354ddaba08a3108f120ff0',
            'aa843fe9284d48529060b852d2e06e68'
        );


        $session->refreshAccessToken($user->getRtoken());

        $options = [
            'auto_refresh' => true,
        ];

        $api = new SpotifyWebAPI($options, $session);

        $api->setSession($session);

        $api->me();

        // Remember to grab the tokens afterwards, they might have been updated
        $user->setAtoken($session->getAccessToken());
        $user->setRtoken($session->getRefreshToken()); // Sometimes, a new refresh token will be returned
        $entityManager->persist($user);

        $entityManager->flush();


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
        $publilikes = $doctrine->getRepository(Like::class)->findBy(['publication' => $publi]);
        $publidislikes = $doctrine->getRepository(Dislike::class)->findBy(['publication' => $publi]);

        $new_acti = new Activite();
        $new_acti->setAuteur($this->getUser());
        $new_acti->setType("Publication");
        $new_acti->setAction("Suppression");
        $new_acti->setDateActivite(new \DateTime());

        $entityManager->persist($new_acti);

        foreach ($publilikes as $like){
            $entityManager->remove($like);
        }
        foreach ($publidislikes as $dislike){
            $entityManager->remove($dislike);
        }

        $entityManager->remove($publi);
        $entityManager->flush();

        return $this->redirectToRoute("Index.index");
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/publication/show/{idpubli}", name="User.showPublication")
     */
    public function showPubli(ManagerRegistry $doctrine, Request $request, Environment $twig, $idpubli = null)
    {
        $publication = $doctrine->getRepository(Publication::class)->findOneBy(['id'=>$idpubli]);

        $defaultData = ['contenu' => ''];
        $form = $this->createFormBuilder($defaultData)
            ->add('contenu', TextareaType::class, [
                'attr' => ['placeholder' => 'Ecrivez votre commentaire', 'autocomplete' => 'off', 'style' => 'resize: none;', 'rows' => 2, 'class' => 'form-control'],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $msg = $form['contenu']->getData();

            $Commentaire = new Commentaire();
            $Commentaire->setContenu($msg);
            $Commentaire->setPublication($publication);
            $Commentaire->setUser($this->getUser());

            $new_acti = new Activite();
            $new_acti->setAuteur($this->getUser());
            $new_acti->setType("Commentaire");
            $new_acti->setAction("Ajout");
            $new_acti->setDateActivite(new \DateTime());

            $manager->persist($new_acti);
            $manager->persist($Commentaire);
            $manager->flush();

            return $this->redirectToRoute('User.showPublication', array('idpubli' => $idpubli));
        }

        if($publication){
            return new Response($twig->render('publication/showPubli.html.twig', ['publication' => $publication, 'user' => $this->getUser(), 'form'=>$form->createView(),]));
        }
        $this->addFlash('danger', 'Publication introuvable');
        return $this->redirectToRoute("Index.index");
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/publication/commentaire/delete/{idpubli}/{idcom}", name="User.deleteCom")
     */
    public function deleteCom(ManagerRegistry $doctrine, Request $request, Environment $twig, $idpubli = null ,$idcom = null){

        $comentaire = $doctrine->getRepository(Commentaire::class)->findOneBy(['id'=>$idcom]);
        $manager = $this->getDoctrine()->getManager();

        $new_acti = new Activite();
        $new_acti->setAuteur($this->getUser());
        $new_acti->setType("Commentaire");
        $new_acti->setAction("Suppression");
        $new_acti->setDateActivite(new \DateTime());

        $manager->persist($new_acti);

        $manager->remove($comentaire);
        $manager->flush();

        return $this->redirectToRoute('User.showPublication', array('idpubli' => $idpubli));
    }
}
