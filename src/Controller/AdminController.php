<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Signalement;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Accès interdit")
     * @Route("/admin", name="Admin.index")
     */
    public function index(ManagerRegistry $doctrine)
    {
        $lastusers = $doctrine->getRepository(User::class)->findBy(
            array('roles' => 'ROLE_USER', 'comfirmed' => 1), // Critere
            array('dateInscription' => 'desc'),        // Tri
            3,                              // Limite
            0                               // Offset
        );

        $lastactivity = $doctrine->getRepository(Activite::class)->findBy(
            array(), // Critere
            array('dateActivite' => 'desc'),        // Tri
            3,                              // Limite
            0                               // Offset
        );

        $lastsignalement = $doctrine->getRepository(Signalement::class)->findBy(
            array(), // Critere
            array('dateSignalement' => 'desc'),        // Tri
            4,                              // Limite
            0                               // Offset
        );


        $countNonRegistered = sizeof($doctrine->getRepository(User::class)->findBy(array('roles' => 'ROLE_USER', 'comfirmed' => 0)));
        $countRegistered = sizeof($doctrine->getRepository(User::class)->findBy(array('roles' => 'ROLE_USER', 'comfirmed' => 1)));
        $countAdmin = sizeof($doctrine->getRepository(User::class)->findBy(array('roles' => 'ROLE_ADMIN', 'comfirmed' => 1)));
        $countsignalement = sizeof($doctrine->getRepository(Signalement::class)->findBy(array('etat' => array('A traiter', 'En cours'))));

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'lastusers' => $lastusers,
            'countnonregister' => $countNonRegistered,
            'countregister' => $countRegistered,
            'countadmin' => $countAdmin,
            'lastactivity' => $lastactivity,
            'lastsignalement' => $lastsignalement,
            'countsignalement' => $countsignalement
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Accès interdit")
     * @Route("/admin/signalements", name="Admin.showSignalements")
     */
    public function showSignalement(ManagerRegistry $doctrine)
    {
        $signalements = $doctrine->getRepository(Signalement::class)->findBy(array(), array('id' => 'DESC'));

        return $this->render('admin/showSignalements.html.twig', [
            'controller_name' => 'AdminController',
            'signalements' => $signalements
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Accès interdit")
     * @Route("/admin/signalements/changestate/{idsign}", name="Admin.signalementChangestate")
     */
    public function signalementChangestate(ManagerRegistry $doctrine, $idsign = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $signalement = $doctrine->getRepository(Signalement::class)->findOneBy(['id'=>$idsign]);
        if($signalement){
            if($signalement->getEtat() == "A traiter"){
                $signalement->setEtat("En cours");
            }
            else if($signalement->getEtat() == "En cours"){
                $signalement->setEtat("Clôt");
            }
            $entityManager->flush();
        }
        return $this->redirectToRoute("Admin.showSignalements");

    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Accès interdit")
     * @Route("/admin/activites", name="Admin.showActivities")
     */
    public function showActivities(ManagerRegistry $doctrine)
    {
        $activites = $doctrine->getRepository(Activite::class)->findBy(array(), array('id' => 'DESC'));

        return $this->render('admin/showActivites.html.twig', [
            'controller_name' => 'AdminController',
            'activities' => $activites
        ]);
    }
}
