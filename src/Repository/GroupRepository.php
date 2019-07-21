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
        //we consider name as an id, since it's unique
        $group = null;
        if (is_numeric($id)) {
            $group = $this->find($id, $lockMode, $lockVersion);
        } else {
            $groupTemp = $this->findBy(['name' => $id]);
            if($groupTemp){
                $group = $groupTemp[0];
            }
        }
        if (is_null($group)) {
            throw new NotFoundHttpException();
        }

        return $group;
    }
}
