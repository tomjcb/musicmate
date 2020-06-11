<?php

namespace App\DataFixtures;

use App\Entity\Activite;
use App\Entity\Conversation;
use App\Entity\Demande;
use App\Entity\Message;
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
        $this->createConv($manager);
        $this->addAmis($manager);
        $this->addDemandes($manager);
        $manager->flush();
    }

    public function addUser(ObjectManager $manager){
        $Users=[
            ['username'=>'admin','password'=>'admin','role'=>'ROLE_ADMIN','nom'=>'ADMIN','prenom'=>'Admin', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => null],
            ['username'=>'client','password'=>'client','role'=>'ROLE_USER','nom'=>'CLIENT','prenom'=>'Client', 'confirmkey' => 12345645, 'confirmed' => 0, 'favgenres' => null],
            ['username'=>'mdupont','password'=>'user','role'=>'ROLE_USER','nom'=>'DUPONT','prenom'=>'Michel', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => 'rock;metal;country'],
            ['username'=>'plaupretre','password'=>'user2','role'=>'ROLE_USER','nom'=>'LAUPRETRE','prenom'=>'Pascal', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => 'EDM;dubstep'],
            ['username'=>'ibort','password'=>'user3','role'=>'ROLE_USER','nom'=>'BORT','prenom'=>'Isabelle', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => 'classique'],
            ['username'=>'nharg','password'=>'user4','role'=>'ROLE_USER','nom'=>'HARG','prenom'=>'Noémie', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => 'orchestral;rock'],
            ['username'=>'ebigre','password'=>'user5','role'=>'ROLE_USER','nom'=>'BIGRE','prenom'=>'Edouard', 'confirmkey' => 12345645, 'confirmed' => 1, 'favgenres' => 'RnB;Hip-Hop']
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
            $new_User->setFavgenres($User['favgenres']);
            $new_User->setIsFirstlogin(1);

            //echo $new_User;
            $manager->persist($new_User);
            $manager->flush();
        }

    }

    public function addPublication(ObjectManager $manager){
        $Publications = [
            ['user'=>'mdupont', 'contenu'=>"J'ai écouté les DaftPunk récemment.. ca faisait longtemps!", 'songlinked' => '1pKYYY0dkg23sQQXi0Q5zN', 'date' => new \DateTime()],
            ['user'=>'plaupretre', 'contenu'=>"J'aime plus trop la musique Rock ...", 'songlinked' => null, 'date' => new \DateTime()],
            ['user'=>'plaupretre', 'contenu'=>"Ecouter du Jazz ? Pourquoi pas", 'songlinked' => null, 'date' => new \DateTime()],
            ['user'=>'nharg', 'contenu'=>"Bonjour tout le monde !", 'songlinked' => null, 'date' => new \DateTime('2020-06-02')],
            ['user'=>'nharg', 'contenu'=>"Je vous partage une musique que j'aime écouter en ce moment", 'songlinked' => '1oYYd2gnWZYrt89EBXdFiO', 'date' => new \DateTime('2020-06-08')],
            ['user'=>'ebigre', 'contenu'=>"Je me demande comment les jeunes font pour écouter du rap", 'songlinked' => null, 'date' => new \DateTime('2020-06-07')]
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
            $new_publi->setSonglinked($Publication['songlinked']);
            $new_publi->setNbLikes(0);
            $new_publi->setNbDislikes(0);
            $new_publi->setDatePublication($Publication['date']);

            $manager->persist($new_publi);
            $manager->persist($new_acti);
            $manager->flush();
        }
    }

    public function createConv(ObjectManager $manager){
        $admin = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'mdupont']);

        $conv1 = new Conversation();
        $conv1->setUser($admin);
        $conv1->setInterlocuteur($user->getUsername());

        $conv2 = new Conversation();
        $conv2->setUser($user);
        $conv2->setInterlocuteur($admin->getUsername());

        $Messages = [
            ['auteur'=>$admin, 'contenu'=>"Bonjour mdupont. Je suis ici pour parler de votre dernier signalement."],
            ['auteur'=>$user, 'contenu'=>"Bonjour admin. Il y a un problème avec mon signalement ?"],
            ['auteur'=>$admin, 'contenu'=>"Mhh oui, j'ai bien l'impression qu'il est injustifié. Qu'en pensez vous ?"],
            ['auteur'=>$user, 'contenu'=>"Il est vrai qu'après y avoir réfléchi, ma réaction était puérile .."],
            ['auteur'=>$admin, 'contenu'=>"Bien, j'annule le signalement alors."],
            ['auteur'=>$user, 'contenu'=>"Oui, ma critique est infondée donc je suis d'accord"],
            ['auteur'=>$admin, 'contenu'=>"Parfait ! Le soucis est réglé alors."],
        ];

        foreach ($Messages as $key => $Message){
            $msg = new Message();
            $msg->setAuteur($Message['auteur']);
            $msg->setContenu($Message['contenu']);
            $msg->setDateMessage(new \DateTime());
            $msg->setIsRead(true);

            $manager->persist($msg);

            $msg2 = new Message();
            $msg2->setAuteur($Message['auteur']);
            $msg2->setContenu($Message['contenu']);
            $msg2->setDateMessage(new \DateTime());
            if($key != count($Messages) - 1){
                $msg2->setIsRead(true);
            }
            else{
                $msg2->setIsRead(false);
            }


            $manager->persist($msg2);

            $conv1->addMessage($msg);
            $conv2->addMessage($msg2);
        }

        $manager->persist($conv1);
        $manager->persist($conv2);

        $admin->addConversation($conv1);
        $user->addConversation($conv2);

        $manager->persist($admin);
        $manager->persist($user);


    }
    public function addDemandes(ObjectManager $manager){
        $mudpont = $manager->getRepository(User::class)->findOneBy(['username' => 'mdupont']);

        $Demande = new Demande();
        $Demande->setFromuser('ibort');
        $Demande->setTouser($mudpont);
        $manager->persist($Demande);

    }

    public function addAmis(ObjectManager $manager){
        $dupont = $manager->getRepository(User::class)->findOneBy(['username' => 'mdupont']);
        $harg = $manager->getRepository(User::class)->findOneBy(['username' => 'nharg']);
        $bigre = $manager->getRepository(User::class)->findOneBy(['username' => 'ebigre']);

        $dupont->addAmi($harg);
        $harg->addAmi($dupont);

        $dupont->addAmi($bigre);
        $bigre->addAmi($dupont);
    }
}
