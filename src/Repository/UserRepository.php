<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function find($id, $lockMode = null, $lockVersion = null)
    {
        //we consider username as an id, since it's unique
        if (is_numeric($id)) {
            $user = parent::find($id, $lockMode, $lockVersion);
        } else {
            $user = $this->findOneBy(['name' => $id]);
        }

        return $user;
    }

    /**
     * @param $id
     * @param $lockMode
     * @param $lockVersion
     * @return User
     */
    public function findOrFail($id, $lockMode = null, $lockVersion = null): User
    {
        $user = $this->find($id, $lockMode, $lockVersion);

        if (is_null($user)) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
