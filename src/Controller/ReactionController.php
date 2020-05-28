<?php


namespace App\Controller;

use App\Entity\Dislike;
use App\Entity\Like;
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

class ReactionController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/user/like/{idpubli}", name="User.like")
     */
    public function like(ManagerRegistry $doctrine, $idpubli = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $publication = $doctrine->getRepository(Publication::class)->findOneBy(['id' => $idpubli]);

        if($publication){
            $like = $doctrine->getRepository(Like::class)->findOneBy(['user' => $this->getUser(), 'publication' => $publication]);
            if(!$like){
                $dislike = $doctrine->getRepository(Dislike::class)->findOneBy(['user' => $this->getUser(), 'publication' => $publication]);
                if($dislike){
                    $entityManager->remove($dislike);
                    $publication->setNbDislikes($publication->getNbDislikes()-1);
                }
                $Like = new Like();
                $Like->setUser($this->getUser());
                $Like->setPublication($publication);

                $entityManager->persist($Like);

                $publication->setNbLikes($publication->getNblikes()+ 1);

                $entityManager->flush();
            }
            else{
                $entityManager->remove($like);
                $publication->setNbLikes($publication->getNbLikes()-1);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute("Index.index");
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/user/dislike/{idpubli}", name="User.dislike")
     */
    public function dislike(ManagerRegistry $doctrine, $idpubli = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $publication = $doctrine->getRepository(Publication::class)->findOneBy(['id' => $idpubli]);

        if($publication){
            $dislike = $doctrine->getRepository(Dislike::class)->findOneBy(['user' => $this->getUser(), 'publication' => $publication]);
            if(!$dislike){
                $like = $doctrine->getRepository(Like::class)->findOneBy(['user' => $this->getUser(), 'publication' => $publication]);
                if($like){
                    $entityManager->remove($like);
                    $publication->setNbLikes($publication->getNbLikes()-1);
                }
                $Dislike = new Dislike();
                $Dislike->setUser($this->getUser());
                $Dislike->setPublication($publication);

                $entityManager->persist($Dislike);

                $publication->setNbDislikes($publication->getNbDislikes()+1);

                $entityManager->flush();
            }
            else{
                $entityManager->remove($dislike);
                $publication->setNbDislikes($publication->getNbDislikes()-1);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute("Index.index");
    }


}
