<?php

namespace Satisfactory\OperationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * OperationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationRepository extends EntityRepository
{
    /**
     * Returns data of the operation 
     * 
     * @param entity $dealing 
     * @return array
     */
    public function getOrderOperations($dealing)
    {
       $qb = $this->createQueryBuilder('o')
                ->select('o')
                ->andWhere('o.dealing =:dealing')
                ->setParameter('dealing', $dealing)
                ->orderBy('o.orderItem','ASC');

        return $qb->getQuery()->getResult() ;
    }
    
    /**
     * Returns data of the operation 
     * 
     * @param entity $dealing 
     * @return array
     */
    public function getAcitfOrderOperations($dealing)
    {
       $qb = $this->createQueryBuilder('o')
                ->select('o')
                ->andWhere('o.dealing =:dealing')
                ->andWhere('o.status =:status')
                ->setParameter('dealing', $dealing)
                ->setParameter('status', 1)
                ->orderBy('o.orderItem','ASC');

        return $qb->getQuery()->getResult() ;
    }
}
