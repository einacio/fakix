<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
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
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return Group|null
     */
    public function find($id, $lockMode = null, $lockVersion = null) : Group
    {
        //we consider name as an id, since it's unique
        if (is_numeric($id)) {
            $group = parent::find($id, $lockMode, $lockVersion);
        } else {
            $group = $this->findOneBy(['name' => $id]);
        }
        return $group;
    }

    /**
     * @param $id
     * @param $lockMode
     * @param $lockVersion
     * @return Group
     */
    public function findOrFail($id, $lockMode = null, $lockVersion = null): Group
    {
        $group = $this->find($id, $lockMode, $lockVersion);
        if (is_null($group)) {
            throw new NotFoundHttpException();
        }

        return $group;
    }
}
