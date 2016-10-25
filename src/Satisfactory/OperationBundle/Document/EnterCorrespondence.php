<?php

namespace Satisfactory\OperationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * Satisfactory\OperationBundle\Document\EnterCorrespondence
 *
 * @MongoDB\Document(repositoryClass="Satisfactory\OperationBundle\Repository\EnterCorrespondenceRepository") 
 */
class EnterCorrespondence
{
    /**
     *
     * @MongoDB\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     *
     * MongoDB\Field(name="index", type="integer")
     */
    protected $index;

    /**
     *
     * @MongoDB\Field(name="input", type="hash")
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    protected $input;

    /**
     *
     * @MongoDB\Field(name="output", type="hash")
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ") 
     */
    protected $output;

    /**
     *
     * @MongoDB\Field(name="correspondenceId", type="integer")
     */
    protected $correspondenceId;
    
    /**
     *
     * @MongoDB\Field(name="createdBy", type="string")
     */
    protected $createdBy;

    /**
     *
     * @MongoDB\Field(name="createdAt", type="date")
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
     * Set input
     *
     * @param hash $input
     * @return self
     */
    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * Get input
     *
     * @return hash $input
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set output
     *
     * @param hash $output
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Get output
     *
     * @return hash $output
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set correspondenceId
     *
     * @param integer $correspondenceId
     * @return self
     */
    public function setCorrespondenceId($correspondenceId)
    {
        $this->correspondenceId = $correspondenceId;
        return $this;
    }

    /**
     * Get correspondenceId
     *
     * @return integer $correspondenceId
     */
    public function getCorrespondenceId()
    {
        return $this->correspondenceId;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return self
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string $createdBy
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
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
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
