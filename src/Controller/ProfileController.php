<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/profil/{userName}", name="User.profil")
     */
    public function index(ManagerRegistry $doctrine, $userName = null)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$userName]);
        if($this->getUser()->getUsername() == $userName){
            return $this->render('profile/index.html.twig', [
                'user' => $user,
                'currentUserProfile' => 'yes'
            ]);
        }
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
