<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    public function findUserLike($keywordss)
{
    $keywords = explode(" ", $keywordss);


    $query = $this->createQueryBuilder('u');
    foreach ($keywords as $key => $keyword) {
        $requ = 'u.nom LIKE :val' . $key;
        $param = 'val' . $key;
        $query->orWhere($requ)
            ->setParameter($param, '%'.$keyword.'%');
    }
    foreach ($keywords as $key => $keyword) {
        $requ = 'u.prenom LIKE :val' . $key;
        $param = 'val' . $key;
        $query->orWhere($requ)
            ->setParameter($param, '%'.$keyword.'%');
    }

    return $query->andWhere('u.roles != :role')
        ->setParameter('role', 'ROLE_ADMIN')
        ->andWhere('u.roles = :role2')
        ->setParameter('role2', 'ROLE_USER')
        ->andWhere('u.comfirmed = :state')
        ->setParameter('state', 1)
        ->orderBy('u.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
        ;
}

    public function findUserGenreLike($keywordss)
    {
        $keywords = explode(" ", $keywordss);


        $query = $this->createQueryBuilder('u');
        foreach ($keywords as $key => $keyword) {
            $requ = 'u.favgenres LIKE :val' . $key;
            $param = 'val' . $key;
            $query->orWhere($requ)
                ->setParameter($param, '%'.$keyword.'%');
        }

        return $query->andWhere('u.roles != :role')
            ->setParameter('role', 'ROLE_ADMIN')
            ->andWhere('u.roles = :role2')
            ->setParameter('role2', 'ROLE_USER')
            ->andWhere('u.comfirmed = :state')
            ->setParameter('state', 1)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
