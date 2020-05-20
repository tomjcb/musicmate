<?php

namespace App\DataFixtures;

use App\Entity\Activite;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
    public function load(ObjectManager $manager)
    {
        $this->addUser($manager);
        $this->addPublication($manager);
        $manager->flush();
    }

    public function addUser(ObjectManager $manager){
        $Users=[
            ['username'=>'admin','password'=>'admin','role'=>'ROLE_ADMIN','nom'=>'ADMIN','prenom'=>'Admin', 'confirmkey' => 12345645, 'confirmed' => 1],
            ['username'=>'client','password'=>'client','role'=>'ROLE_USER','nom'=>'CLIENT','prenom'=>'Client', 'confirmkey' => 12345645, 'confirmed' => 0],
            ['username'=>'user','password'=>'user','role'=>'ROLE_USER','nom'=>'USER','prenom'=>'User', 'confirmkey' => 12345645, 'confirmed' => 1],
            ['username'=>'user2','password'=>'user2','role'=>'ROLE_USER','nom'=>'USER2','prenom'=>'User2', 'confirmkey' => 12345645, 'confirmed' => 1],
            ['username'=>'user3','password'=>'user3','role'=>'ROLE_USER','nom'=>'USER3','prenom'=>'User3', 'confirmkey' => 12345645, 'confirmed' => 1],
            ['username'=>'user4','password'=>'user4','role'=>'ROLE_USER','nom'=>'USER4','prenom'=>'User4', 'confirmkey' => 12345645, 'confirmed' => 1]
        ];

        foreach ($Users as $User){
            $new_User = new User();
            $new_User->setUsername($User['username']);
            $password = $this->passwordEncoder->encodePassword($new_User, $User['password']);
            $new_User->setPassword($password);
            //$roles = array($User['role']);
            $new_User->setRoles($User['role']);
            //$new_User->setRoles(array_unique($roles));
            $new_User->setDateInscription(new \DateTime());
            $new_User->setNom($User['nom']);
            $new_User->setPrenom($User['prenom']);
            $new_User->setConfirmKey($User['confirmkey']);
            $new_User->setComfirmed($User['confirmed']);
            $new_User->setMail("example@email.fr");

            //echo $new_User;
            $manager->persist($new_User);
            $manager->flush();
        }

    }

    public function addPublication(ObjectManager $manager){
        $Publications = [
            ['user'=>'user', 'contenu'=>"J'ai écouté les DaftPunk récemment.. ca faisait longtemps!"],
            ['user'=>'user2', 'contenu'=>"J'aime plus trop la musique Rock ..."],
            ['user'=>'user2', 'contenu'=>"Ecouter du Jazz ? Pourquoi pas"]
        ];

        foreach ($Publications as $Publication){
            $new_publi = new Publication();
            $new_acti = new Activite();
            $user = $manager->getRepository(User::class)->findOneBy(['username' => $Publication['user']]);
            $new_publi->setAuteur($user);
            $new_acti->setAuteur($user);
            $new_acti->setType("Publication");
            $new_acti->setAction("Ajout");
            $new_acti->setDateActivite(new \DateTime());
            $new_publi->setContenu($Publication['contenu']);
            $new_publi->setNbLikes(0);
            $new_publi->setNbDislikes(0);

            $manager->persist($new_publi);
            $manager->persist($new_acti);
            $manager->flush();
        }
    }
}
