<?php

namespace Satisfactory\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cronexecution
 *
 * @ORM\Table(name="satisfactory_cronexecution")
 * @ORM\Entity(repositoryClass="Satisfactory\CronBundle\Repository\CronexecutionRepository")
 */
class Cronexecution
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
     * @var string
     *
     * @ORM\Column(name="dealingName", type="string", nullable=true)
     */
    private $dealingName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="client", type="string", nullable=true)
     */
    private $client;

    /**
     * @var int
     *
     * @ORM\Column(name="beginAt", type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @var int
     *
     * @ORM\Column(name="endAt", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="array", nullable=true)
     */
    private $log;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="array", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="running", type="integer")
     */
    private $running;

    /**
     * @var string
     *
     * @ORM\Column(name="executionDuration", type="string", nullable=true)
     */
    private $executionDuration;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=true)
     */
    private $result;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nbLineIn", type="integer", nullable=true)
     */
    private $nbLineIn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nbLineOut", type="integer", nullable=true)
     */
    private $nbLineOut;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\CronBundle\Entity\CronexecutionOperation", mappedBy="cronexecution", cascade={"persist"})
     */
    private $cronexecutionOperations;
    
    /**
    * 
    *
    * @ORM\Column(name="runningPid", type="integer", nullable=true)
    */
    private $runningPid;
    
    /**
    * 
    *
    * @ORM\Column(name="isExecutedManually", type="boolean", nullable=true)
    */
    private $isExecutedManually;
    
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set beginAt
     *
     * @param \DateTime $beginAt
     * @return Cronexecution
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
     * @return Cronexecution
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
     * Set log
     *
     * @param array $log
     * @return Cronexecution
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return array 
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set error
     *
     * @param array $error
     * @return Cronexecution
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return array 
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set running
     *
     * @param boolean $running
     * @return Cronexecution
     */
    public function setRunning($running)
    {
        $this->running = $running;

        return $this;
    }

    /**
     * Get running
     *
     * @return boolean 
     */
    public function getRunning()
    {
        return $this->running;
    }
    
    /**
     * Get runningPid
     *
     * @return boolean 
     */
    public function getRunningPid()
    {
        return $this->runningPid;
    }
    
    /**
     * Set running Pid
     *
     * @param integer $runningPid
     * @return Cronexecution
     */
    public function setRunningPid($runningPid)
    {
        $this->runningPid = $runningPid;

        return $this;
    }

    /**
     * Set executionDuration
     *
     * @param integer $executionDuration
     * @return Cronexecution
     */
    public function setExecutionDuration($executionDuration)
    {
        $this->executionDuration = $executionDuration;

        return $this;
    }

    /**
     * Get executionDuration
     *
     * @return integer 
     */
    public function getExecutionDuration()
    {
        return $this->executionDuration;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Cronexecution
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
     * Set nbLineIn
     *
     * @param integer $nbLineIn
     * @return Cronexecution
     */
    public function setNbLineIn($nbLineIn)
    {
        $this->nbLineIn = $nbLineIn;

        return $this;
    }

    /**
     * Get nbLineIn
     *
     * @return integer 
     */
    public function getNbLineIn()
    {
        return $this->nbLineIn;
    }

    /**
     * Set nbLineOut
     *
     * @param integer $nbLineOut
     * @return Cronexecution
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
     * Set dealing
     *
     * @param \Satisfactory\OperationBundle\Entity\Dealing $dealing
     * @return Cronexecution
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

    /**
     * Add operations
     *
     * @param \Satisfactory\CronBundle\Entity\CronexecutionOperation $operations
     * @return Cronexecution
     */
    public function addOperation(\Satisfactory\CronBundle\Entity\CronexecutionOperation $operations)
    {
        $this->operations[] = $operations;

        return $this;
    }

    /**
     * Remove operations
     *
     * @param \Satisfactory\CronBundle\Entity\CronexecutionOperation $operations
     */
    public function removeOperation(\Satisfactory\CronBundle\Entity\CronexecutionOperation $operations)
    {
        $this->operations->removeElement($operations);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Add cronexecutionOperations
     *
     * @param \Satisfactory\CronBundle\Entity\CronexecutionOperation $cronexecutionOperations
     * @return Cronexecution
     */
    public function addCronexecutionOperation(\Satisfactory\CronBundle\Entity\CronexecutionOperation $cronexecutionOperations)
    {
        $this->cronexecutionOperations[] = $cronexecutionOperations;

        return $this;
    }

    /**
     * Remove cronexecutionOperations
     *
     * @param \Satisfactory\CronBundle\Entity\CronexecutionOperation $cronexecutionOperations
     */
    public function removeCronexecutionOperation(\Satisfactory\CronBundle\Entity\CronexecutionOperation $cronexecutionOperations)
    {
        $this->cronexecutionOperations->removeElement($cronexecutionOperations);
    }

    /**
     * Get cronexecutionOperations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCronexecutionOperations()
    {
        return $this->cronexecutionOperations;
    }

    /**
     * Set dealingName
     *
     * @param string $dealingName
     * @return Cronexecution
     */
    public function setDealingName($dealingName)
    {
        $this->dealingName = $dealingName;

        return $this;
    }

    /**
     * Get dealingName
     *
     * @return string 
     */
    public function getDealingName()
    {
        return $this->dealingName;
    }

    /**
     * Set client
     *
     * @param string $client
     * @return Cronexecution
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
     * Set result
     *
     * @param integer $result
     * @return Cronexecution
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set isExecutedManually
     *
     * @param boolean $isExecutedManually
     * @return Cronexecution
     */
    public function setIsExecutedManually($isExecutedManually)
    {
        $this->isExecutedManually = $isExecutedManually;

        return $this;
    }

    /**
     * Get isExecutedManually
     *
     * @return boolean 
     */
    public function getIsExecutedManually()
    {
        return $this->isExecutedManually;
    }
}
