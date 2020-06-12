<?php


namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\GoogleUserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FriendController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/amis", name="User.showFriends")
     */
    public function showFriends(ManagerRegistry $doctrine)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')){
            return $this->redirectToRoute("User.setusername");
        }



        return $this->render('friends/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/firstlogin/addfriends", name="User.firstlogin")
     */
    public function addNewFriends(ManagerRegistry $doctrine, UserRepository $userRepository)
    {
        $ppltoadd = null;
            $genres = str_replace(';', ' ', $this->getUser()->getFavgenres());

            $ppltoadd = $userRepository->findUserGenreLike($genres);



        return $this->render('registered/firstlogin.html.twig', [
            'user' => $this->getUser(),
            'ppltoadd' => $ppltoadd
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ami/delete/{username}", name="User.deleteFriend")
     */
    public function deleteFriend(ManagerRegistry $doctrine, $username = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $usertodel = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        $this->getUser()->removeAmi($usertodel);
        $usertodel->removeAmi($this->getUser());

        $entityManager->flush();



        return $this->render('friends/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
