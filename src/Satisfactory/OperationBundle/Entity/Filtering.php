<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Filtering
 *
 * @ORM\Table(name="satisfactory_filtering_client")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\FilteringRepository")
 */
class Filtering
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User $client
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="client", referencedColumnName="id", nullable=false)
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sharingData", type="string", length=255, nullable=true)
     */
    private $sharingData;

    /**
     * @var string
     *
     * @ORM\Column(name="columnName", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $columnName;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;
    
    /**
     * @ORM\Column(name="dateStart", type="datetime", nullable=true)
     */
    private $dateStart;
    
    /**
     * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
     */
    private $dateEnd;
    
     /**
     * @var User $createdBy
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var User $updatedBy
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
     */
    private $updatedBy;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="creator_name", type="text", nullable=true)
     */
    private $creatorName;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Operation", mappedBy="filtering", cascade={"persist"})
     */
    private $operation;
    
    /**
     * To string
     * 
     */
    public function __toString() {
        if($this->getType() == 1)
            $type = 'ne conserver que les lignes avec cette valeur';
        elseif($this->getType() == 2)
            $type = 'exclure les lignes avec cette valeur';
        
        return $this->getName()." - ".$type;
    }

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
     * Set client
     *
     * @param string $client
     * @return Filtering
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Filtering
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sharingData
     *
     * @param string $sharingData
     * @return Filtering
     */
    public function setSharingData($sharingData)
    {
        $this->sharingData = $sharingData;

        return $this;
    }

    /**
     * Get sharingData
     *
     * @return string 
     */
    public function getSharingData()
    {
        return $this->sharingData;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     * @return Filtering
     */
    public function setClomunName($columnName)
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get columnName
     *
     * @return string 
     */
    public function getClomunName()
    {
        return $this->columnName;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Filtering
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Filtering
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Filtering
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return Filtering
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Filtering
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

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Filtering
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $createdBy
     * @return Filtering
     */
    public function setCreatedBy(\Satisfactory\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Satisfactory\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $updatedBy
     * @return Filtering
     */
    public function setUpdatedBy(\Satisfactory\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Satisfactory\UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     * @return Filtering
     */
    public function setColomunName($columnName)
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get columnName
     *
     * @return string 
     */
    public function getColomunName()
    {
        return $this->columnName;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     * @return Filtering
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get columnName
     *
     * @return string 
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * Set creatorName
     *
     * @param string $creatorName
     * @return Filtering
     */
    public function setCreatorName($creatorName)
    {
        $this->creatorName = $creatorName;

        return $this;
    }

    /**
     * Get creatorName
     *
     * @return string 
     */
    public function getCreatorName()
    {
        return $this->creatorName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Filtering
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operation = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add operation
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operation
     * @return Filtering
     */
    public function addOperation(\Satisfactory\OperationBundle\Entity\Operation $operation)
    {
        $this->operation[] = $operation;

        return $this;
    }

    /**
     * Remove operation
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operation
     */
    public function removeOperation(\Satisfactory\OperationBundle\Entity\Operation $operation)
    {
        $this->operation->removeElement($operation);
    }

    /**
     * Get operation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOperation()
    {
        return $this->operation;
    }
}
