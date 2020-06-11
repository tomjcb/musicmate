<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\GoogleUserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use function GuzzleHttp\Psr7\build_query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class SearchController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/search/{type}/{keyword}", name="User.search")
     */
    public function search(ManagerRegistry $doctrine, UserRepository $userRepository ,$type = null, $keyword = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if($type == "Utilisateur"){
            $usersfound = $userRepository->findUserLike($keyword);
            return $this->render('search/index.html.twig', [
                'user' => $this->getUser(),
                'users' => $usersfound
            ]);
        }
        if($type == "Genres-musicaux"){
            $usersfound = $userRepository->findUserGenreLike($keyword);
            return $this->render('search/index.html.twig', [
                'user' => $this->getUser(),
                'users' => $usersfound
            ]);
        }
        if($type == "Publication"){
            $usersfound = $userRepository->findUserLike($keyword);
            return $this->render('search/index.html.twig', [
                'user' => $this->getUser(),
                'users' => $usersfound
            ]);
        }

        return $this->redirectToRoute("Index.index");
    }


}
