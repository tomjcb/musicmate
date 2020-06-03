<?php


namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Publication;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConvController extends AbstractController
{
    /**
     * @Route("/messages", name="Conv.conv")
     */
    public function conv(Request $request,ManagerRegistry $doctrine, $cible = null)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')) {
            return $this->redirectToRoute("User.setusername");
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            //$conv = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser(), 'interlocuteur' => 'admin'));
            $allconv = $doctrine->getRepository(Conversation::class)->findBy(['user' => $this->getUser()]);
            return $this->render('conv/index.html.twig', [
                'user' => $this->getUser(),
                'allconv' => $allconv
            ]);
        }
    }

    /**
     * @Route("/messages/createconv/{cible}", name="Conv.createconv")
     */
    public function createconv(Request $request,ManagerRegistry $doctrine, $cible = null)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')) {
            return $this->redirectToRoute("User.setusername");
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $existingconv = $doctrine->getRepository(Conversation::class)->findOneBy(['user' => $this->getUser(), 'interlocuteur' => $cible]);
            if($existingconv){
                return $this->redirectToRoute('Conv.showconv', array('cible' => $cible));
            }
            else{
                $manager = $this->getDoctrine()->getManager();
                $usercible = $doctrine->getRepository(User::class)->findOneBy(['username' => $cible]);

                $conv1 = new Conversation();
                $conv1->setUser($this->getUser());
                $conv1->setInterlocuteur($cible);

                $conv2 = new Conversation();
                $conv2->setUser($usercible);
                $conv2->setInterlocuteur($this->getUser()->getUsername());

                $manager->persist($conv1);
                $manager->persist($conv2);

                $manager->flush();
            }
            return $this->redirectToRoute('Conv.showconv', array('cible' => $cible));
        }
    }


    /**
     * @Route("/messages/{cible}", name="Conv.showconv")
     */
    public function index(Request $request,ManagerRegistry $doctrine, $cible = null)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_NEEDUSERNAME')) {
            return $this->redirectToRoute("User.setusername");
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
        $allconv = $doctrine->getRepository(Conversation::class)->findBy(['user' => $this->getUser()]);
        $conv = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser(), 'interlocuteur' => $cible));
        $msg = $conv->getMessages();
        $manager = $this->getDoctrine()->getManager();
        foreach ($msg as $message){
            $message->setIsRead(1);
            $manager->persist($message);
        }
        $manager->flush();
        //$conv = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser()));

        $defaultData = ['message' => ''];
        $form = $this->createFormBuilder($defaultData)
            ->add('message', TextType::class, [
                'attr' => ['placeholder' => 'Ecrivez votre message', 'autocomplete' => 'off'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $msg = $form['message']->getData();
                $usercible = $doctrine->getRepository(User::class)->findOneBy(['username' => $cible]);

                if($usercible){
                    $conv1 = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser(), 'interlocuteur' => $usercible->getUsername()));
                    $conv2 = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $usercible, 'interlocuteur' => $this->getUser()->getUsername()));

                    $msg1 = new Message();
                    $msg1->setAuteur($this->getUser());
                    $msg1->setContenu($msg);
                    $msg1->setDateMessage(new \DateTime());
                    $msg1->setIsRead(false);

                    $manager->persist($msg1);

                    $msg2 = new Message();
                    $msg2->setAuteur($this->getUser());
                    $msg2->setContenu($msg);
                    $msg2->setDateMessage(new \DateTime());
                    $msg2->setIsRead(false);

                    $manager->persist($msg2);

                    $conv1->addMessage($msg1);
                    $conv2->addMessage($msg2);

                    $manager->persist($conv1);
                    $manager->persist($conv2);

                    $manager->flush();

                    return $this->redirectToRoute('Conv.showconv', array('cible' => $usercible->getUsername()));


                }
            }

        return $this->render('conv/index.html.twig', [
            'form'=>$form->createView(),
            'user' => $this->getUser(),
            'allconv' => $allconv,
            'conv' => $conv,
            'active' => 1
        ]);
        }
        else {
            return $this->render('index/index.html.twig', [
                'controller_name' => 'IndexController',
            ]);
        }

    }

    /**
     * @Route("/message/send/{msg}/{cible}/", name="Conv.sendmsg")
     */
    public function sendmsg(ManagerRegistry $doctrine, $cible = null, $msg = null){
        $manager = $this->getDoctrine()->getManager();
        $usercible = $doctrine->getRepository(User::class)->findOneBy(['username' => $cible]);

        if($usercible){
            $conv1 = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $this->getUser()));
            $conv2 = $doctrine->getRepository(Conversation::class)->findOneBy(array('user' => $usercible));

            $msg1 = new Message();
            $msg1->setAuteur($this->getUser());
            $msg1->setContenu($msg);
            $msg1->setDateMessage(new \DateTime());
            $msg1->setIsRead(false);

            $manager->persist($msg1);

            $msg2 = new Message();
            $msg2->setAuteur($this->getUser());
            $msg2->setContenu($msg);
            $msg2->setDateMessage(new \DateTime());
            $msg2->setIsRead(false);

            $manager->persist($msg2);

            $conv1->addMessage($msg1);
            $conv2->addMessage($msg2);

            $manager->persist($conv1);
            $manager->persist($conv2);

            $manager->flush();
        }
        return $this->redirectToRoute("Conv.conv");

    }
}
