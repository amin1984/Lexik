<?php

namespace Satisfactory\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manualexecutedealing
 *
 * @ORM\Table(name="satisfactory_manualexecutedealing")
 * @ORM\Entity(repositoryClass="Satisfactory\CronBundle\Repository\ManualexecutedealingRepository")
 */
class Manualexecutedealing
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
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Dealing", inversedBy="cron", cascade={"persist"})
     * @ORM\JoinColumn(name="dealing", referencedColumnName="id", nullable=false)
     */
    private $dealing;

    /**
     * @var int
     *
     * @ORM\Column(name="beginAt", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Manualexecutedealing
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
     * @return Manualexecutedealing
     */
    public function setDealing(\Satisfactory\OperationBundle\Entity\Dealing $dealing)
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
}
