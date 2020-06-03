<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SpotifyController extends AbstractController
{


    /**
     * @Route("/spotify", name="spotify")
     */
    public function index()
    {
        $session = new Session(
            'c1d54f3ca5354ddaba08a3108f120ff0',
            'aa843fe9284d48529060b852d2e06e68',
            'http://localhost:8000/spotify/callback'
        );
        $options = [
            'scope' => [
                'playlist-read-private',
                'user-read-private',
                'user-read-currently-playing',
                'user-modify-playback-state',
                'user-read-playback-state',
            ],
        ];
        header('Location: ' . $session->getAuthorizeUrl($options));
        die();
    }

    /**
     * @Route("/spotify/callback", name="spotify_callback")
     */
    public function callback(ManagerRegistry $doctrine)
    {
        $manager = $this->getDoctrine()->getManager();
        $session = new Session(
            'c1d54f3ca5354ddaba08a3108f120ff0',
            'aa843fe9284d48529060b852d2e06e68',
            'http://localhost:8000/spotify/callback'
        );
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' =>$this->getUser()->getUsername()]);

        // Request a access token using the code from Spotify
        $session->requestAccessToken($_GET['code']);

        $user->setAtoken($session->getAccessToken());
        $user->setRtoken($session->getRefreshToken());

        $manager->persist($user);

        $manager->flush();

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

// Store the access and refresh tokens somewhere. In a database for example.

// Send the user along and fetch some data!
        return $this->redirectToRoute('spotify_app', array('auth' => $accessToken));
    }
    /**
     * @Route("/spotify/app/{search}", name="spotify_app")
     */
    public function spotindex(ManagerRegistry $doctrine, Request $request ,$search = null)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' =>$this->getUser()->getUsername()]);
        $manager = $this->getDoctrine()->getManager();
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
        $manager->persist($user);

        $manager->flush();


        $me = $api->me();

        if($search != null){
            return $this->render('spotify/result.html.twig', [
                'auth' => $user->getAtoken(),
                'search' => $search,
                'user' => $this->getUser(),
                'me' => $me,
                'current' => $api->getMyCurrentTrack(),
            ]);
        }


        $defaultData = ['search' => ''];
        $form = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class, [
                'attr' => ['placeholder' => 'Chercher une musique', 'autocomplete' => 'off'],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $src = $form['search']->getData();
            return $this->render('spotify/result.html.twig', [
                'auth' => $user->getAtoken(),
                'search' => $src,
                'user' => $this->getUser(),
                'me' => $me,
                'current' => $api->getMyCurrentTrack(),
                'form'=>$form->createView(),
            ]);

        }

        return $this->render('spotify/index.html.twig', [
            'auth' => $user->getAtoken(),
            'user' => $this->getUser(),
            'me' => $me,
            'current' => $api->getMyCurrentTrack(),
            'form'=>$form->createView(),
        ]);
    }
}
