<?php

namespace Satisfactory\CronBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Satisfactory\CronBundle\Document\RejectLog
 * 
 * @ODM\Document
 * @ODM\Document(repositoryClass="Satisfactory\CronBundle\Repository\RejectLogRepository")
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")  
 */
class RejectLog
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
     * @var string $idDealing
     *
     * @ODM\Field(name="idDealing", type="integer")
     */
    protected $idDealing;

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
     * Set idDealing
     *
     * @param integer $idDealing
     * @return self
     */
    public function setIdDealing($idDealing)
    {
        $this->idDealing = $idDealing;
        return $this;
    }

    /**
     * Get idDealing
     *
     * @return integer $idDealing
     */
    public function getIdDealing()
    {
        return $this->idDealing;
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
