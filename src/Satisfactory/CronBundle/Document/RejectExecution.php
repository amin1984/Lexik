<?php

namespace Satisfactory\CronBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Satisfactory\CronBundle\Document\RejectExecution
 * 
 * @ODM\Document(repositoryClass="Satisfactory\CronBundle\Repository\RejectExecutionRepository")
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")  
 */
class RejectExecution
{
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var integer $index
     *
     * @ODM\Field(name="index", type="integer")
     */
    protected $index;

    /**
     * @var string $idOperation
     *
     * @ODM\Field(name="idOperation", type="integer")
     */
    protected $idOperation;

    /**
     * @var string $columnContent
     *
     * @ODM\Field(name="columnContent", type="string")
     */
    protected $columnContent;

    /**
     * @var hash $content
     *
     * @ODM\Field(name="content", type="hash")
     */
    protected $content;

    /**
     * @var string $createdAt
     *
     * @ODM\Field(name="createdAt", type="string")
     */
    protected $createdAt;
    
    

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set index
     *
     * @param integer $index
     * @return self
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Get index
     *
     * @return integer $index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set idOperation
     *
     * @param integer $idOperation
     * @return self
     */
    public function setIdOperation($idOperation)
    {
        $this->idOperation = $idOperation;
        return $this;
    }

    /**
     * Get idOperation
     *
     * @return integer $idOperation
     */
    public function getIdOperation()
    {
        return $this->idOperation;
    }

    /**
     * Set columnContent
     *
     * @param string $columnContent
     * @return self
     */
    public function setColumnContent($columnContent)
    {
        $this->columnContent = $columnContent;
        return $this;
    }

    /**
     * Get columnContent
     *
     * @return string $columnContent
     */
    public function getColumnContent()
    {
        return $this->columnContent;
    }

    /**
     * Set content
     *
     * @param hash $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return hash $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
