<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Reject
 *
 * @ORM\Table(name="satisfactory_reject")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\RejectRepository")
 */
class Reject
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ruleName", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $ruleName;

    /**
     * @var string
     *
     * @ORM\Column(name="columnName", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $columnName;

    /**
     * @var int
     *
     * @ORM\Column(name="maxProcess", type="integer", nullable=true)
     */
    private $maxProcess;

    /**
     * @var string
     *
     * @ORM\Column(name="processBy", type="string", length=255, nullable=true)
     */
    private $processBy;
    
    /**
     * @var int
     *
     * @ORM\Column(name="processDay", type="integer", nullable=true)
     */
    private $processDay;
    
    /**
     * @var int
     *
     * @ORM\Column(name="processWeek", type="integer", nullable=true)
     */
    private $processWeek;
    
    /**
     * @var int
     *
     * @ORM\Column(name="processMonth", type="integer", nullable=true)
     */
    private $processMonth;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;
    
    /**
     * @var file $file
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="numberMaxToSend", type="integer", nullable=true)
     */
    private $numberMaxToSend;

    /**
     * @var int
     *
     * @ORM\Column(name="periodOfSend", type="integer", nullable=true)
     */
    private $periodOfSend;

    /**
     * @var string
     *
     * @ORM\Column(name="typeSendQuota", type="string", length=255, nullable=true)
     */
    private $typeSendQuota;

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
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
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
     * @var User $client
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", inversedBy="reject", cascade={"persist"})
     * @ORM\JoinColumn(name="client", referencedColumnName="id", nullable=false)
     */
    private $client;
    
     /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Operation", mappedBy="reject", cascade={"persist"})
     */
    private $operation;
    
    /**
     * To string
     * 
     */
    public function __toString() {
        return $this->getRuleName()." - ".$this->getType();
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
     * Set type
     *
     * @param string $type
     * @return Reject
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
     * Set maxProcess
     *
     * @param integer $maxProcess
     * @return Reject
     */
    public function setMaxProcess($maxProcess)
    {
        $this->maxProcess = $maxProcess;

        return $this;
    }

    /**
     * Get maxProcess
     *
     * @return integer 
     */
    public function getMaxProcess()
    {
        return $this->maxProcess;
    }

    /**
     * Set processBy
     *
     * @param string $processBy
     * @return Reject
     */
    public function setProcessBy($processBy)
    {
        $this->processBy = $processBy;

        return $this;
    }

    /**
     * Get processBy
     *
     * @return string 
     */
    public function getProcessBy()
    {
        return $this->processBy;
    }

    /**
     * Set processDay
     *
     * @param integer $processDay
     * @return Reject
     */
    public function setProcessDay($processDay)
    {
        $this->processDay = $processDay;

        return $this;
    }

    /**
     * Get processDay
     *
     * @return integer 
     */
    public function getProcessDay()
    {
        return $this->processDay;
    }

    /**
     * Set processWeek
     *
     * @param integer $processWeek
     * @return Reject
     */
    public function setProcessWeek($processWeek)
    {
        $this->processWeek = $processWeek;

        return $this;
    }

    /**
     * Get processWeek
     *
     * @return integer 
     */
    public function getProcessWeek()
    {
        return $this->processWeek;
    }

    /**
     * Set processMonth
     *
     * @param integer $processMonth
     * @return Reject
     */
    public function setProcessMonth($processMonth)
    {
        $this->processMonth = $processMonth;

        return $this;
    }

    /**
     * Get processMonth
     *
     * @return integer 
     */
    public function getProcessMonth()
    {
        return $this->processMonth;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Reject
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Set periodOfSend
     *
     * @param integer $periodOfSend
     * @return Reject
     */
    public function setPeriodOfSend($periodOfSend)
    {
        $this->periodOfSend = $periodOfSend;

        return $this;
    }

    /**
     * Get periodOfSend
     *
     * @return integer 
     */
    public function getPeriodOfSend()
    {
        return $this->periodOfSend;
    }

    /**
     * Set typeSendQuota
     *
     * @param string $typeSendQuota
     * @return Reject
     */
    public function setTypeSendQuota($typeSendQuota)
    {
        $this->typeSendQuota = $typeSendQuota;

        return $this;
    }

    /**
     * Get typeSendQuota
     *
     * @return string 
     */
    public function getTypeSendQuota()
    {
        return $this->typeSendQuota;
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
     * Upload file
     */
     public function getAbsolutePath()
    {
        return null === $this->file
            ? null
            : $this->getUploadRootDir().'/'.$this->file;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }
    
    
    /**
     * Get upload dir. This function is sespecially for cron to return only the path of the upload so the cron scrip can catch the dir of the upload od rejects files
     *
     * @return string upload dir path
     */
    public function getWebPathForCron()
    {
        return $this->getUploadDir().'/';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/rejects';
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = $this->file->getBasename()."_".$this->file->getClientOriginalName();
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

    /**
     * Set numberMaxToSend
     *
     * @param integer $numberMaxToSend
     * @return Reject
     */
    public function setNumberMaxToSend($numberMaxToSend)
    {
        $this->numberMaxToSend = $numberMaxToSend;

        return $this;
    }

    /**
     * Get numberMaxToSend
     *
     * @return integer 
     */
    public function getNumberMaxToSend()
    {
        return $this->numberMaxToSend;
    }

    /**
     * Set creatorName
     *
     * @param string $creatorName
     * @return Reject
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Reject
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
     * @return Reject
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
     * @return Reject
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
     * @return Reject
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
     * Set client
     *
     * @param \Satisfactory\UserBundle\Entity\User $client
     * @return Reject
     */
    public function setClient(\Satisfactory\UserBundle\Entity\User $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Satisfactory\UserBundle\Entity\User 
     */
    public function getClient()
    {
        return $this->client;
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
     * @return Reject
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

    /**
     * Set ruleName
     *
     * @param string $ruleName
     * @return Reject
     */
    public function setRuleName($ruleName)
    {
        $this->ruleName = $ruleName;

        return $this;
    }

    /**
     * Get ruleName
     *
     * @return string 
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     * @return Reject
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
}
