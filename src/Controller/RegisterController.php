<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="User.register")
     */
    public function index(ManagerRegistry $doctrine, Request $request, Environment $twig, UserPasswordEncoderInterface $encoder,  \Swift_Mailer $mailer)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $this->addFlash('success', 'Accès impossible : vous êtes déjà connecté.');
            return $this->redirectToRoute("Index.index");
            //throw $this->createAccessDeniedException('GET OUT!');
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')){
            return $this->redirectToRoute("User.setusername");
        }



        $entityManager = $this->getDoctrine()->getManager();
        $User = new User();
        $form = $this->createForm(UserType::class, $User);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees['username']=$form['_username']->getData();
            $donnees['password']=$form['_password']->getData();
            $donnees['nom']=$form['nom']->getData();
            $donnees['prenom']=$form['prenom']->getData();
            $donnees['mail']=$form['mail']->getData();


            $erreurs=$this->validDonnees($donnees, $doctrine);

            if (empty($erreurs)) {

                $passwordEncoded = $encoder->encodePassword($User, $User->getPassword());
                $User->setPassword($passwordEncoded);

                $longueurKey = 12;
                $key = "";

                for($i=1; $i < $longueurKey; $i++){
                    $key .= mt_rand(0,9);
                }
                $User->setConfirmKey($key);
                $User->setComfirmed("0");
                $User->setRoles('ROLE_USER');
                $User->setIsFirstlogin(0);

                $entityManager->persist($User);
                $entityManager->flush();
                $this->addFlash('success', $form['prenom']->getData().' ' . $form['nom']->getData() . ' votre inscription est un succès. Regardez vos mails pour activer votre compte');

                $message = (new \Swift_Message('Confirmation de votre inscription'))
                    ->setFrom(['MusicMate90@gmail.com' => 'MusicMate'])
                    ->setTo('tom.jacob@outlook.fr')
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'swift_mailer/index.html.twig',
                            ['mail' => urlencode($donnees['username']), 'key' => urlencode($key), 'nom' => $donnees['nom'], 'prenom' => $donnees['prenom']]
                        ),
                        'text/html'
                    )
                ;

                $mailer->send($message);

                return $this->render('index/index.html.twig');

            }
            return new Response($twig->render('register/index.html.twig',['form'=>$form->createView(), 'erreurs'=>$erreurs]));
        }
        return new Response($twig->render('register/index.html.twig',['form'=>$form->createView()]));
        //return $this->render('register/index.html.twig', ['controller_name' => 'IndexController',]);
    }

    private function validDonnees($donnees, $doctrine)
    {
        $erreurs=array();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$donnees['username']]);

        if($user){
            $erreurs['_username']="Nom d'utilisateur déjà pris.";
        }


        // Test sur le nom
        if (! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))
            $erreurs['nom']='Votre nom doit être composé de 2 caractères minimum';

        // Test sur le prénom
        if (! preg_match("/^[A-Za-z ]{2,}/",$donnees['prenom']))
            $erreurs['prenom']='Votre prénom doit être composé de 2 caractères minimum';

        // Test sur l'id
        if(isset($donnees['id']) and !is_numeric($donnees['id']) )
            $erreurs['id']='type id incorrect';
        return $erreurs;

    }
}
