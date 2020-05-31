<?php


namespace App\Controller;

use App\Entity\Demande;
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

class DemandeController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/demandeami/{userName}", name="User.demandeami")
     */
    public function makedemande(ManagerRegistry $doctrine, $userName = null)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $userName]);
        $demande = $doctrine->getRepository(Demande::class)->findOneBy(['fromuser' => $this->getUser(), 'touser' => $userName]);
        if ($this->getUser()->getUsername() == $userName || !$user) {
            return $this->redirectToRoute("Index.index");
        }
        if($demande){
            $this->addFlash('danger', 'Vous avez déjà fait une demande à cet utilisateur');
            return $this->redirectToRoute("Index.index");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $newDemande = new Demande();
        $newDemande->setFromuser($this->getUser()->getUsername());
        $newDemande->setTouser($user);

        $entityManager->persist($newDemande);

        $user->addDemande($newDemande);

        $entityManager->flush();

        return $this->redirectToRoute('User.profil', array('userName' => $userName));
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/showdemandes", name="User.showDemandes")
     */
    public function showDemandes(ManagerRegistry $doctrine)
    {   $users = [];
        $demandes = $doctrine->getRepository(Demande::class)->findBy(['touser' => $this->getUser()]);
        foreach ($demandes as $demande){
            $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $demande->getFromuser()]);
            array_push($users, $user);
        }

        return $this->render('friendrequest/index.html.twig', [
            'user' => $this->getUser(),
            'demandes' => $demandes,
            'users' => $users
        ]);
    }
}
