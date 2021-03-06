<?php

namespace Satisfactory\CronBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * RejectExecusionRepository
 *
 * This class was generated by the ODM. Add your own custom
 * repository methods below.
 */
    class RejectExecutionRepository extends DocumentRepository
{
    
    /**
     * @param nombre de jour int $nbJours 
     *
     * @return Correspondence entity
     */
    public function findByDate($nbJours,$idOperation) 
    {
        $mongoDate= new \MongoDate(strtotime( date('Y-m-d H:i:s',(\time() - ($nbJours * 86400))) ) );

        $query = $this->createQueryBuilder('RejectExecution')
                    ->field('createdAt')->gte(date('Y-m-d H:i:s', $mongoDate->sec))
                    ->field('idOperation')->equals($idOperation)
                    ->hydrate(false)
                ;
        return $query->getQuery()->execute();
    } 
}
