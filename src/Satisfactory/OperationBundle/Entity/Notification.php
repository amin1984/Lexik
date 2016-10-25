<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_notification")
 */
class Notification
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
     * @ORM\Column(name="email", type="string", nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $email;
    
    /**
     * @var array
     *
     * @ORM\Column(name="type", type="array", nullable=true)
     */
    private $type;
    
    /**
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Dealing", inversedBy="notifications", cascade={"persist"})
     * @ORM\JoinColumn(name="dealing", referencedColumnName="id", nullable=false)
     */
    private $dealing;
    
     /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    public function __construct()
    {
         $this->createdAt = new \DateTime();
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
     * Set email
     *
     * @param string $email
     * @return Notification
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set type
     *
     * @param array $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return array 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Notification
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
     * Set dealing
     *
     * @param \Satisfactory\OperationBundle\Entity\Dealing $dealing
     * @return Notification
     */
    public function setDealing(\Satisfactory\OperationBundle\Entity\Dealing $dealing = null)
    {
        $this->dealing = $dealing;

        return $this;
    }

    /**
     * Get dealing
     *
     * @return \Satisfactory\OperationBundle\Entity\Dealing 
     */
    public function getDealing()
    {
        return $this->dealing;
    }
    
    public function __toString() 
    {
        return $this->getEmail();
    }
}
