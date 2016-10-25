<?php

namespace Satisfactory\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_cronexecutionOperation")
 * @ORM\Entity(repositoryClass="Satisfactory\CronBundle\Repository\CronexecutionOperationRepository")
 */
class CronexecutionOperation
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
     * @ORM\ManyToOne("Satisfactory\CronBundle\Entity\Cronexecution", inversedBy="cronexecutionOperations", cascade={"persist"})
     * @ORM\JoinColumn(name="cronexecution", referencedColumnName="id", nullable=true)
     */
    private $cronexecution;
    
    /**
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Operation", inversedBy="cronexecutionOperations", cascade={"persist"})
     * @ORM\JoinColumn(name="operation", referencedColumnName="id", nullable=true,onDelete="CASCADE")
     */
    private $operation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="operationName", type="string", nullable=true)
     */
    private $operationName;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="beginAt", type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="endAt", type="datetime", nullable=true)
     */
    private $endAt;
    /**
     * @var status
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbLineOut", type="integer", nullable=true)
     */
    private $nbLineOut;
    
    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", nullable=true)
     */
    private $file;
    
    /**
     * @var array
     *
     * @ORM\Column(name="log", type="array", nullable=true)
     */
    private $log;

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
     * Set beginAt
     *
     * @param \DateTime $beginAt
     * @return CronexecutionOperation
     */
    public function setBeginAt($beginAt)
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * Get beginAt
     *
     * @return \DateTime 
     */
    public function getBeginAt()
    {
        return $this->beginAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return CronexecutionOperation
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CronexecutionOperation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set nbLineOut
     *
     * @param integer $nbLineOut
     * @return CronexecutionOperation
     */
    public function setNbLineOut($nbLineOut)
    {
        $this->nbLineOut = $nbLineOut;

        return $this;
    }

    /**
     * Get nbLineOut
     *
     * @return integer 
     */
    public function getNbLineOut()
    {
        return $this->nbLineOut;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return CronexecutionOperation
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set cronexecution
     *
     * @param \Satisfactory\OperationBundle\Entity\Cronexecution $cronexecution
     * @return CronexecutionOperation
     */
    public function setCronexecution(\Satisfactory\CronBundle\Entity\Cronexecution $cronexecution = null)
    {
        $this->cronexecution = $cronexecution;

        return $this;
    }

    /**
     * Get cronexecution
     *
     * @return \Satisfactory\OperationBundle\Entity\Cronexecution 
     */
    public function getCronexecution()
    {
        return $this->cronexecution;
    }

    /**
     * Set operation
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operation
     * @return CronexecutionOperation
     */
    public function setOperation(\Satisfactory\OperationBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \Satisfactory\OperationBundle\Entity\Dealing 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set operationName
     *
     * @param string $operationName
     * @return CronexecutionOperation
     */
    public function setOperationName($operationName)
    {
        $this->operationName = $operationName;

        return $this;
    }

    /**
     * Get operationName
     *
     * @return string 
     */
    public function getOperationName()
    {
        return $this->operationName;
    }

    /**
     * Set log
     *
     * @param string $log
     * @return CronexecutionOperation
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return string 
     */
    public function getLog()
    {
        return $this->log;
    }
}
