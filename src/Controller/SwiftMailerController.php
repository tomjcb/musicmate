<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SwiftMailerController extends AbstractController
{
    /**
     * @Route("/swift/mailer", name="swift_mailer")
     */
    public function index($mail, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Confirmation de votre inscription'))
            ->setFrom('MusicMate90@gmail.com')
            ->setTo('tom.jacob@outlook.fr')
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'swift_mailer/index.html.twig'
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->render('index/index.html.twig');
    }
}
