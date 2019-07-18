<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @param $id
     * @param $lockMode
     * @param $lockVersion
     * @return Group
     */
    public function findOrFail($id, $lockMode = null, $lockVersion = null): Group
    {
        //we consider username as an id, since it's unique
        if (is_numeric($id)) {
            $user = $this->find($id, $lockMode, $lockVersion);
        } else {
            $user = $this->findBy(['name' => $id]);
        }
        if (is_null($user)) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
