<?php

namespace Satisfactory\OperationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DealingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DealingRepository extends EntityRepository
{
     /**
     * Returns data of the operation 
     * 
     * @param integer $dealing : id of the generated flight
     * @return array
     */
    public function getOperationByOrder($dealing, $order)
    {
       $qb = $this->createQueryBuilder('o')
                ->select('o')
                ->andWhere('o.dealing =:dealing')
                ->andWhere('o.orderItem =:order')
                ->setParameter('order', $order)
                ->setParameter('dealing', $dealing);

        return $qb->getQuery()->getArrayResult() ;
    }
    
     /**
     * @param User entity $client
     * @param string $expression
     *
     * @return array
     */
    public function searchByClient($client, $expression) 
    {
        
        $qb = $this->createQueryBuilder('d')
                ->select('d')
                ->where('d.client = :client')
                ->andWhere('d.name LIKE :expression')
                ->setParameter('client', $client)
                ->setParameter('expression', '%' . $expression . '%');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * @param string $expression
     *
     * @return array
     */
    public function findWithSearch($expression) 
    {
        
        $qb = $this->createQueryBuilder('d')
                ->select('d')
                ->where('d.name LIKE :expression')
                ->setParameter('expression', '%' . $expression . '%');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Count the number of element in the entity
     *
     * @return int
     */
    public function countQuery()
    {
         $qb = $this->createQueryBuilder('d')
                ->select('count(d.id)');
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * @param $limit
     * @param $offset
     * @param $tag
     *
     * @return array
     */
    public function paginatorQuery($limit, $offset = 0, $input, $client, $order, $orderType)
    {
        
        $qb = $this->createQueryBuilder('d');

        if ($input) {
            $qb->andWhere('d.name LIKE :name')
                    ->setParameter('name', '%' . $input . '%');
        }
        
        if ($client <> 'all') {
            $qb->andWhere('d.client =:client')
                    ->setParameter('client', $client);
        }

        if ($limit) {
            $qb->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
        $qb->orderBy('d.'.$order, $orderType);

        return $qb->getQuery()->getResult();
    }
    
    /**
     * @param $offset
     * @param $tag
     *
     * @return array
     */
    public function paginatorQueryLimitOff( $offset = 0, $input, $client, $order, $orderType)
    {
        
        $qb = $this->createQueryBuilder('d');

        if ($input) {
            $qb->andWhere('d.name LIKE :name')
                    ->setParameter('name', '%' . $input . '%');
        }
        
        if ($client <> 'all') {
            $qb->andWhere('d.client =:client')
                    ->setParameter('client', $client);
        }
        
        $qb->orderBy('d.'.$order, $orderType);

        return $qb->getQuery()->getResult();
    }
}
