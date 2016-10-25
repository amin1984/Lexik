<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_entercorrespondence")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\CorrespondanceRepository")
 */
class EnterCorrespondance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="index", type="integer")
     */
    private $index;
    
     /**
     * @ORM\Column(name="input", type="string", length=255, nullable=true)
     */
    private $input;
    
     /**
     * @ORM\Column(name="output", type="string", length=255, nullable=true)
     */
    private $output;
    
    /**
     *
     * @ORM\Column(name="correspondenceId", type="integer")
     */
    protected $correspondenceId;
    
    /**
     *
     * @ORM\Column(name="createdBy", type="string")
     */
    protected $createdBy;

    /**
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set index
     *
     * @param integer $index
     * @return EnterCorrespondance
     */
    public function setIndex($index)
    {
        $this->index = $index;
    
        return $this;
    }

    /**
     * Get index
     *
     * @return integer 
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set input
     *
     * @param string $input
     * @return EnterCorrespondance
     */
    public function setInput($input)
    {
        $this->input = $input;
    
        return $this;
    }

    /**
     * Get input
     *
     * @return string 
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set output
     *
     * @param string $output
     * @return EnterCorrespondance
     */
    public function setOutput($output)
    {
        $this->output = $output;
    
        return $this;
    }

    /**
     * Get output
     *
     * @return string 
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set correspondenceId
     *
     * @param integer $correspondenceId
     * @return EnterCorrespondance
     */
    public function setCorrespondenceId($correspondenceId)
    {
        $this->correspondenceId = $correspondenceId;
    
        return $this;
    }

    /**
     * Get correspondenceId
     *
     * @return integer 
     */
    public function getCorrespondenceId()
    {
        return $this->correspondenceId;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return EnterCorrespondance
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return EnterCorrespondance
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
