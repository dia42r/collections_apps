<?php

namespace AppBundle\Repository;

/**
 * ItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCollections()
    {
        $qb = $this->createQueryBuilder('i');
        $qb
            ->select('i.collection')
            ->groupBy('i.collection')
        ;

        return $qb->getQuery()->getResult();
        
    }
}
