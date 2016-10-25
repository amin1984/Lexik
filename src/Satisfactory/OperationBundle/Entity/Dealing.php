<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_dealing")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\DealingRepository")
 */
class Dealing
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
     * @ORM\Column(name="name", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $name;
    
    /**
     * @var User $client
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", inversedBy="dealing", cascade={"persist"})
     * @ORM\JoinColumn(name="client", referencedColumnName="id", nullable=false)
     */
    private $client;
    
     /**
     * @var string
     *
     * @ORM\Column(name="protocol", type="string", nullable=true)
     */
    private $protocol;
    
    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $host;
    
    /**
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $port;
    
    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $login;
    
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $password;
    
    /**
     * @var string
     *
     * @ORM\Column(name="directory", type="string", nullable=true)
     */
    private $directory;
    
    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $fileName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sepa", type="string", nullable=true)
     */
    private $sepa;
    
    /**
     * @var string
     *
     * @ORM\Column(name="other", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $other;
    
    /**
     * @var string
     *
     * @ORM\Column(name="quotation", type="string", nullable=true)
     */
    private $quotation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="encoding", type="string", nullable=true)
     */
    private $encoding;
    
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
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Notification", mappedBy="dealing", cascade={"remove","persist"})
     */
    private $notifications;
    
    /**
     * @var string
     *
     * @ORM\Column(name="recurence", type="string", nullable=true)
     */
    private $recurence;
    
    /**
     * @var array
     *
     * @ORM\Column(name="days", type="array", nullable=true)
     */
    private $days;
    
    /**
     *
     * @ORM\Column(name="time_day", type="datetime", nullable=true)
     */
    private $timeDay;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="day_of_month", type="integer", nullable=true)
     * @Assert\Range(
     *      min = "1",
     *      max = "31" 
     * ) 
     */
    private $dayOfMonth;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Operation", mappedBy="dealing", cascade={"persist"})
     */
    private $operations;
    
    /**
     *
     * @ORM\Column(name="time_month", type="datetime", nullable=true)
     */
    private $timeMonth;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;
    
    /**
     * @var string
     *
     * @ORM\Column(name="creator_name", type="text", nullable=true)
     */
    private $creatorName;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\CronBundle\Entity\Cronexecution", mappedBy="dealing", cascade={"remove","persist"})
     */
    private $cron;
    
    /**
     * @ORM\Column(name="executed_at", type="datetime", nullable=true)
     */
    private $executedAt;
    
    /**
     * @ORM\Column(name="executed_status", type="integer", nullable=true)
     */
    private $executedStatus;
    
    /**
     *
     * @ORM\Column(name="nb_ligne_to_delete", type="integer", nullable=false)
     */
    private $nbLigneToDelete;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_file_header", type="boolean")
     */
    private $isFileHeader;
    
    /**
     *
     * @ORM\Column(name="file_header", type="text", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $fileHeader;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="delete_semicolon", type="boolean")
     */
    private $deleteSemicolon;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isCompressed", type="boolean", nullable=true)
     */
    private $isCompressed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="compressionFormat", type="string", nullable=true)
     */
    private $compressionFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="compressionFile", type="string", nullable=true)
     */
    private $compressionFile;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isFileNameWildcard", type="boolean", nullable=true)
     */
    private $isFileNameWildcard;
    
     public function __construct()
    {
         $this->protocol = 'ftp';
         $this->other = 'autre';
         $this->notification = new ArrayCollection();
         $this->depot = false;
         $this->isActive = false;
         $this->fileHeader = 'O-T';
         $this->isCompressed = 0;
         $this->compressionFile = 'O-T';
    }
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Assert\Callback(array(
            'methods' => array('validate'),
        )));
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
     * Set name
     *
     * @param string $name
     * @return Dealing
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
     * Set protocol
     *
     * @param string $protocol
     * @return Dealing
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return string 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return Dealing
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Dealing
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer 
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return Dealing
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Dealing
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set directory
     *
     * @param string $directory
     * @return Dealing
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return string 
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Dealing
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set sepa
     *
     * @param string $sepa
     * @return Dealing
     */
    public function setSepa($sepa)
    {
        $this->sepa = $sepa;

        return $this;
    }

    /**
     * Get sepa
     *
     * @return string 
     */
    public function getSepa()
    {
        return $this->sepa;
    }

    /**
     * Set other
     *
     * @param string $other
     * @return Dealing
     */
    public function setOther($other)
    {
        $this->other = $other;

        return $this;
    }

    /**
     * Get other
     *
     * @return string 
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Set quotation
     *
     * @param string $quotation
     * @return Dealing
     */
    public function setQuotation($quotation)
    {
        $this->quotation = $quotation;

        return $this;
    }

    /**
     * Get quotation
     *
     * @return string 
     */
    public function getQuotation()
    {
        return $this->quotation;
    }

    /**
     * Set encoding
     *
     * @param string $encoding
     * @return Dealing
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Get encoding
     *
     * @return string 
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Dealing
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
     * @return Dealing
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
     * Set daily
     *
     * @param integer $daily
     * @return Dealing
     */
    public function setDaily($daily)
    {
        $this->daily = $daily;

        return $this;
    }

    /**
     * Get daily
     *
     * @return integer 
     */
    public function getDaily()
    {
        return $this->daily;
    }

    /**
     * Set days
     *
     * @param array $days
     * @return Dealing
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return array 
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set timeDay
     *
     * @param integer $timeDay
     * @return Dealing
     */
    public function setTimeDay($timeDay)
    {
        $this->timeDay = $timeDay;

        return $this;
    }

    /**
     * Get timeDay
     *
     * @return integer 
     */
    public function getTimeDay()
    {
        return $this->timeDay;
    }

    /**
     * Set monthly
     *
     * @param integer $monthly
     * @return Dealing
     */
    public function setMonthly($monthly)
    {
        $this->monthly = $monthly;

        return $this;
    }

    /**
     * Get monthly
     *
     * @return integer 
     */
    public function getMonthly()
    {
        return $this->monthly;
    }

    /**
     * Set dayOfMonth
     *
     * @param integer $dayOfMonth
     * @return Dealing
     */
    public function setDayOfMonth($dayOfMonth)
    {
        $this->dayOfMonth = $dayOfMonth;

        return $this;
    }

    /**
     * Get dayOfMonth
     *
     * @return integer 
     */
    public function getDayOfMonth()
    {
        return $this->dayOfMonth;
    }

    /**
     * Set timeMonth
     *
     * @param integer $timeMonth
     * @return Dealing
     */
    public function setTimeMonth($timeMonth)
    {
        $this->timeMonth = $timeMonth;

        return $this;
    }

    /**
     * Get timeMonth
     *
     * @return integer 
     */
    public function getTimeMonth()
    {
        return $this->timeMonth;
    }

    /**
     * Set depot
     *
     * @param boolean $depot
     * @return Dealing
     */
    public function setDepot($depot)
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * Get depot
     *
     * @return boolean 
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * Set client
     *
     * @param \Satisfactory\UserBundle\Entity\User $client
     * @return Dealing
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
     * Set createdBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $createdBy
     * @return Dealing
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
     * @return Dealing
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
     * Add notifications
     *
     * @param \Satisfactory\OperationBundle\Entity\Notification $notifications
     * @return Dealing
     */
    public function addNotification(\Satisfactory\OperationBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Satisfactory\OperationBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Satisfactory\OperationBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set recurence
     *
     * @param string $recurence
     * @return Dealing
     */
    public function setRecurence($recurence)
    {
        $this->recurence = $recurence;

        return $this;
    }

    /**
     * Get recurence
     *
     * @return string 
     */
    public function getRecurence()
    {
        return $this->recurence;
    }

    /**
     * Add operations
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operations
     * @return Dealing
     */
    public function addOperation(\Satisfactory\OperationBundle\Entity\Operation $operations)
    {
        $this->operations[] = $operations;

        return $this;
    }

    /**
     * Remove operations
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operations
     */
    public function removeOperation(\Satisfactory\OperationBundle\Entity\Operation $operations)
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Dealing
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
     * Set creatorName
     *
     * @param string $creatorName
     * @return Dealing
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
     * Add cron
     *
     * @param \Satisfactory\CronBundle\Entity\Cronexecution $cron
     * @return Dealing
     */
    public function addCron(\Satisfactory\CronBundle\Entity\Cronexecution $cron)
    {
        $this->cron[] = $cron;

        return $this;
    }

    /**
     * Remove cron
     *
     * @param \Satisfactory\CronBundle\Entity\Cronexecution $cron
     */
    public function removeCron(\Satisfactory\CronBundle\Entity\Cronexecution $cron)
    {
        $this->cron->removeElement($cron);
    }

    /**
     * Get cron
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCron()
    {
        return $this->cron;
    }

    /**
     * Set executedAt
     *
     * @param \DateTime $executedAt
     * @return Dealing
     */
    public function setExecutedAt($executedAt)
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    /**
     * Get executedAt
     *
     * @return \DateTime 
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * Set executedStatus
     *
     * @param integer $executedStatus
     * @return Dealing
     */
    public function setExecutedStatus($executedStatus)
    {
        $this->executedStatus = $executedStatus;

        return $this;
    }

    /**
     * Get executedStatus
     *
     * @return integer 
     */
    public function getExecutedStatus()
    {
        return $this->executedStatus;
    }

    /**
     * Set nbLigneToDelete
     *
     * @param integer $nbLigneToDelete
     * @return Dealing
     */
    public function setNbLigneToDelete($nbLigneToDelete)
    {
        $this->nbLigneToDelete = $nbLigneToDelete;

        return $this;
    }

    /**
     * Get nbLigneToDelete
     *
     * @return integer 
     */
    public function getNbLigneToDelete()
    {
        return $this->nbLigneToDelete;
    }

    /**
     * Set isFileHeader
     *
     * @param boolean $isFileHeader
     * @return Dealing
     */
    public function setIsFileHeader($isFileHeader)
    {
        $this->isFileHeader = $isFileHeader;

        return $this;
    }

    /**
     * Get isFileHeader
     *
     * @return boolean 
     */
    public function getIsFileHeader()
    {
        return $this->isFileHeader;
    }

    /**
     * Set fileHeader
     *
     * @param string $fileHeader
     * @return Dealing
     */
    public function setFileHeader($fileHeader)
    {
        $this->fileHeader = $fileHeader;

        return $this;
    }

    /**
     * Get fileHeader
     *
     * @return string 
     */
    public function getFileHeader()
    {
        return $this->fileHeader;
    }

    /**
     * Set deleteSemicolon
     *
     * @param boolean $deleteSemicolon
     * @return Dealing
     */
    public function setDeleteSemicolon($deleteSemicolon)
    {
        $this->deleteSemicolon = $deleteSemicolon;

        return $this;
    }

    /**
     * Get deleteSemicolon
     *
     * @return boolean 
     */
    public function getDeleteSemicolon()
    {
        return $this->deleteSemicolon;
    }
    

    /**
     * Set isCompressed
     *
     * @param boolean $isCompressed
     * @return Dealing
     */
    public function setIsCompressed($isCompressed)
    {
        $this->isCompressed = $isCompressed;

        return $this;
    }

    /**
     * Get isCompressed
     *
     * @return boolean 
     */
    public function getIsCompressed()
    {
        return $this->isCompressed;
    }

    /**
     * Set compressionFormat
     *
     * @param string $compressionFormat
     * @return Dealing
     */
    public function setCompressionFormat($compressionFormat)
    {
        $this->compressionFormat = $compressionFormat;

        return $this;
    }

    /**
     * Get compressionFormat
     *
     * @return string 
     */
    public function getCompressionFormat()
    {
        return $this->compressionFormat;
    }

    /**
     * Set compressionFile
     *
     * @param string $compressionFile
     * @return Dealing
     */
    public function setCompressionFile($compressionFile)
    {
        $this->compressionFile = $compressionFile;

        return $this;
    }

    /**
     * Get compressionFile
     *
     * @return string 
     */
    public function getCompressionFile()
    {
        return $this->compressionFile;
    }

    /**
     * Set isFileNameWildcard
     *
     * @param boolean $isFileNameWildcard
     * @return Dealing
     */
    public function setIsFileNameWildcard($isFileNameWildcard)
    {
        $this->isFileNameWildcard = $isFileNameWildcard;

        return $this;
    }

    /**
     * Get isFileNameWildcard
     *
     * @return boolean 
     */
    public function getIsFileNameWildcard()
    {
        return $this->isFileNameWildcard;
    }
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getIsCompressed() && $this->getCompressionFile()=="") {
            $context->buildViolation('Champ obligatoire ! ')
                ->atPath('compressionFile')
                ->addViolation();
        }
    }
}
