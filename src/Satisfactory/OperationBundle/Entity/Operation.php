<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_operation")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\OperationRepository")
 */
class Operation
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
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Dealing", inversedBy="operations", cascade={"persist"})
     * @ORM\JoinColumn(name="dealing", referencedColumnName="id", nullable=true)
     */
    private $dealing;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     * @Assert\NotBlank(message="Vous devez choisir un type d'opération ! ") 
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50 , nullable=true)
     * @Assert\Length(
     *      max = 50
     * ) 
     */
    private $description;
    
    /**
     * @var User $createdBy
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;
    
     /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filterColumnName", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $filterColumnName;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="filterType", type="boolean", nullable=true)
     */
    private $filterType;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filterValue", type="string", nullable=true)
     * @Assert\NotBlank(message="Il faut choisir une option si dessous !")
     */
    private $filterValue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyStructure", type="string", nullable=true)
     * @Assert\NotBlank(message="Il faut choisir une option si dessus !")
     */
    private $modifyStructure;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyNameColumnToRename", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $modifyNameColumnToRename;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyNameColumnRename", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $modifyNameColumnRename;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyNameColumnToSort", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $modifyNameColumnToSort;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyTypeSort", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $modifyTypeSort;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyNameColumnToAdded", type="text", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $modifyNameColumnToAdded;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyAddedPosition", type="string", nullable=true)
     */
    private $modifyAddedPosition;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modifyNameColumnPosition", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire")
     */
    private $modifyNameColumnPosition;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="orderItem", type="integer", nullable=true)
     */
    private $orderItem;
    
    /**
     * @var string
     *
     * @ORM\Column(name="duplicateNameColumn", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $duplicateNameColumn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="duplicateKeep", type="string", nullable=true)
     */
    private $duplicateKeep;
    
    /**
     * @var string
     *
     * @ORM\Column(name="enrichFilter", type="string", nullable=true)
     */
    private $enrichFilter;
    
    /**
     * @var string
     *
     * @ORM\Column(name="enrichColumnName", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $enrichColumnName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="enrichColumnNameSource", type="string", nullable=true)
     */
    private $enrichColumnNameSource;
    
    /**
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Correspondance", inversedBy="operations", cascade={"persist"})
     * @ORM\JoinColumn(name="correspondance", referencedColumnName="id", nullable=true)
     */
    private $correspondance;
    
    /**
     * @var string
     *
     * @ORM\Column(name="enrichColumnNameRuleSource", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $enrichColumnNameRuleSource;
    
    /**
     * @var string
     *
     * @ORM\Column(name="enrichRule", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire !")
     */
    private $enrichRule;
    
     /**
     * @var string
     *
     * @ORM\Column(name="enrichColumnFilter", type="string", nullable=true)
     */
    private $enrichColumnFilter;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichValue", type="string", nullable=true)
     */
    private $enrichValue;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichEgal", type="string", nullable=true)
     */
    private $enrichEgal;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichDifferent", type="string", nullable=true)
     */
    private $enrichDifferent;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichSuperiorOrEgal", type="string", nullable=true)
     */
    private $enrichSuperiorOrEgal;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichInferiorOrEgal", type="string", nullable=true)
     */
    private $enrichInferiorOrEgal;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichIncludingMin", type="string", nullable=true)
     */
    private $enrichIncludingMin;
     /**
     * @var string
     *
     * @ORM\Column(name="enrichIncludingMax", type="string", nullable=true)
     */
    private $enrichIncludingMax;
     /**
     * @var string
     *
     * @ORM\Column(name="publishProtocol", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishProtocol;
    
     /**
     * @var string
     *
     * @ORM\Column(name="publishHost", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishHost;
    
    /**
     *
     * @ORM\Column(name="publishPort", type="integer", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishPort;
    
    /**
     * @var string
     *
     * @ORM\Column(name="publishLogin", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishLogin;
    
    /**
     * @var string
     *
     * @ORM\Column(name="publishPassword", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishPassword;
    
    /**
     * @var string
     *
     * @ORM\Column(name="publishDirectory", type="string", nullable=true)
     */
    private $publishDirectory;
    
    /**
     * @var string
     *
     * @ORM\Column(name="publishFileName", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $publishFileName;
    
   /**
     *
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Reject", inversedBy="operation", cascade={"persist"})
     * @ORM\JoinColumn(name="reject", referencedColumnName="id", nullable=true)
     */
    private $reject;
    
    /**
     *
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Filtering", inversedBy="operation", cascade={"persist"})
     * @ORM\JoinColumn(name="filtering", referencedColumnName="id", nullable=true)
     */
    private $filtering;
    
    /**
     * @var string
     *
     * @ORM\Column(name="columnToDelete", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $columnToDelete;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nameColumnPosition", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $nameColumnPosition;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reorderPosition", type="string", nullable=true)
     */
    private $reorderPosition;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reorderColumnName", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $reorderColumnName;
    
    /**
     * @var text
     *
     * @ORM\Column(name="json", type="text", nullable=true)
     */
    private $json;
    
    /**
     * @var text
     *
     * @ORM\Column(name="jsonCorrespondence", type="text", nullable=true)
     */
    private $jsonCorrespondence;
    
    /**
     * @var array
     *
     * @ORM\Column(name="columns_checked", type="array")
     */
    private $columnsChecked;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nameColumnFormat", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $nameColumnFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="columnFormat", type="string", nullable=true)
     */
    private $columnFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="targetFormatPhone", type="string", nullable=true)
     */
    private $targetFormatPhone;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="countryCode", type="integer", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $countryCode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="newFormat", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $newFormat;
    
     /**
     *
     * @ORM\Column(name="sourceFormatDate", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $sourceFormatDate;
    
    /**
     *
     * @ORM\Column(name="targetFormatDate", type="string", nullable=true)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $targetFormatDate;
    
    /**
     *
     * @ORM\Column(name="targetColumn", type="string", nullable=true)
     */
    private $targetColumn;
    
    /**
     * @var array
     *
     * @ORM\Column(name="filterManualColumns", type="array")
     */
    private $filterManualColumns;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\CronBundle\Entity\CronexecutionOperation", mappedBy="operation", cascade={"persist"})
     */
    private $cronexecutionOperations;
    
    
    public function __construct()
    {
         $this->createdAt = new \DateTime();
         $this->status = false;
         $this->filterColumnName = 'O-T';
         $this->type = 'O-T';
         $this->filterType = false;
         $this->modifyTypeSort = 1;
         $this->duplicateNameColumn = 'O-T';
         $this->modifyStructure = 'O-T';
         $this->modifyNameColumnRename = 'O-T';
         $this->modifyNameColumnToRename = 'O-T';
         $this->modifyNameColumnToSort = 'O-T';
         $this->modifyNameColumnToAdded = 'O-T';
         $this->modifyNameColumnPosition = 'O-T';
         $this->filterValue = 'O-T';
         $this->enrichColumnName = 'O-T';
         $this->enrichColumnNameSource = 'O-T';
         $this->enrichColumnNameRuleSource = 'O-T';
         $this->enrichRule = 'O-T';
         $this->publishProtocol = 'O-T';
         $this->publishHost = 'O-T';
         $this->publishPort = 'O-T';
         $this->publishLogin = 'O-T';
         $this->publishPassword = 'O-T';
         $this->publishFileName = 'O-T';
         $this->columnToDelete = 'O-T';
         $this->nameColumnPosition = 'O-T';
         $this->reorderColumnName = 'O-T';
         $this->nameColumnFormat = 'O-T';
         $this->columnFormat = 1;
         $this->targetFormatPhone = 1;
         $this->countryCode = 0;
         $this->newFormat = 'O-T';
         $this->sourceFormatDate = 'O-T';
         $this->targetFormatDate = 'O-T';
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
     * @return Operation
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
     * Set description
     *
     * @param string $description
     * @return Operation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Operation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Operation
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
     * Set filterColumnName
     *
     * @param string $filterColumnName
     * @return Operation
     */
    public function setFilterColumnName($filterColumnName)
    {
        $this->filterColumnName = $filterColumnName;

        return $this;
    }

    /**
     * Get filterColumnName
     *
     * @return string 
     */
    public function getFilterColumnName()
    {
        return $this->filterColumnName;
    }

    /**
     * Set filterType
     *
     * @param boolean $filterType
     * @return Operation
     */
    public function setFilterType($filterType)
    {
        $this->filterType = $filterType;

        return $this;
    }

    /**
     * Get filterType
     *
     * @return boolean 
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * Set filterValue
     *
     * @param string $filterValue
     * @return Operation
     */
    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;

        return $this;
    }

    /**
     * Get filterValue
     *
     * @return string 
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * Set modifyStructure
     *
     * @param string $modifyStructure
     * @return Operation
     */
    public function setModifyStructure($modifyStructure)
    {
        $this->modifyStructure = $modifyStructure;

        return $this;
    }

    /**
     * Get modifyStructure
     *
     * @return string 
     */
    public function getModifyStructure()
    {
        return $this->modifyStructure;
    }

    /**
     * Set modifyNameColumnToRename
     *
     * @param string $modifyNameColumnToRename
     * @return Operation
     */
    public function setModifyNameColumnToRename($modifyNameColumnToRename)
    {
        $this->modifyNameColumnToRename = $modifyNameColumnToRename;

        return $this;
    }

    /**
     * Get modifyNameColumnToRename
     *
     * @return string 
     */
    public function getModifyNameColumnToRename()
    {
        return $this->modifyNameColumnToRename;
    }

    /**
     * Set modifyNameColumnRename
     *
     * @param string $modifyNameColumnRename
     * @return Operation
     */
    public function setModifyNameColumnRename($modifyNameColumnRename)
    {
        $this->modifyNameColumnRename = $modifyNameColumnRename;

        return $this;
    }

    /**
     * Get modifyNameColumnRename
     *
     * @return string 
     */
    public function getModifyNameColumnRename()
    {
        return $this->modifyNameColumnRename;
    }

    /**
     * Set modifyNameColumnToSort
     *
     * @param string $modifyNameColumnToSort
     * @return Operation
     */
    public function setModifyNameColumnToSort($modifyNameColumnToSort)
    {
        $this->modifyNameColumnToSort = $modifyNameColumnToSort;

        return $this;
    }

    /**
     * Get modifyNameColumnToSort
     *
     * @return string 
     */
    public function getModifyNameColumnToSort()
    {
        return $this->modifyNameColumnToSort;
    }

    /**
     * Set modifyTypeSort
     *
     * @param string $modifyTypeSort
     * @return Operation
     */
    public function setModifyTypeSort($modifyTypeSort)
    {
        $this->modifyTypeSort = $modifyTypeSort;

        return $this;
    }

    /**
     * Get modifyTypeSort
     *
     * @return string 
     */
    public function getModifyTypeSort()
    {
        return $this->modifyTypeSort;
    }

    /**
     * Set modifyNameColumnToAdded
     *
     * @param string $modifyNameColumnToAdded
     * @return Operation
     */
    public function setModifyNameColumnToAdded($modifyNameColumnToAdded)
    {
        $this->modifyNameColumnToAdded = $modifyNameColumnToAdded;

        return $this;
    }

    /**
     * Get modifyNameColumnToAdded
     *
     * @return string 
     */
    public function getModifyNameColumnToAdded()
    {
        return $this->modifyNameColumnToAdded;
    }

    /**
     * Set modifyAddedPosition
     *
     * @param string $modifyAddedPosition
     * @return Operation
     */
    public function setModifyAddedPosition($modifyAddedPosition)
    {
        $this->modifyAddedPosition = $modifyAddedPosition;

        return $this;
    }

    /**
     * Get modifyAddedPosition
     *
     * @return string 
     */
    public function getModifyAddedPosition()
    {
        return $this->modifyAddedPosition;
    }

    /**
     * Set modifyNameColumnPosition
     *
     * @param string $modifyNameColumnPosition
     * @return Operation
     */
    public function setModifyNameColumnPosition($modifyNameColumnPosition)
    {
        $this->modifyNameColumnPosition = $modifyNameColumnPosition;

        return $this;
    }

    /**
     * Get modifyNameColumnPosition
     *
     * @return string 
     */
    public function getModifyNameColumnPosition()
    {
        return $this->modifyNameColumnPosition;
    }

    /**
     * Set orderItem
     *
     * @param integer $orderItem
     * @return Operation
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * Get orderItem
     *
     * @return integer 
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * Set duplicateNameColumn
     *
     * @param string $duplicateNameColumn
     * @return Operation
     */
    public function setDuplicateNameColumn($duplicateNameColumn)
    {
        $this->duplicateNameColumn = $duplicateNameColumn;

        return $this;
    }

    /**
     * Get duplicateNameColumn
     *
     * @return string 
     */
    public function getDuplicateNameColumn()
    {
        return $this->duplicateNameColumn;
    }

    /**
     * Set duplicateKeep
     *
     * @param string $duplicateKeep
     * @return Operation
     */
    public function setDuplicateKeep($duplicateKeep)
    {
        $this->duplicateKeep = $duplicateKeep;

        return $this;
    }

    /**
     * Get duplicateKeep
     *
     * @return string 
     */
    public function getDuplicateKeep()
    {
        return $this->duplicateKeep;
    }

    /**
     * Set enrichFilter
     *
     * @param string $enrichFilter
     * @return Operation
     */
    public function setEnrichFilter($enrichFilter)
    {
        $this->enrichFilter = $enrichFilter;

        return $this;
    }

    /**
     * Get enrichFilter
     *
     * @return string 
     */
    public function getEnrichFilter()
    {
        return $this->enrichFilter;
    }

    /**
     * Set enrichColumnName
     *
     * @param string $enrichColumnName
     * @return Operation
     */
    public function setEnrichColumnName($enrichColumnName)
    {
        $this->enrichColumnName = $enrichColumnName;

        return $this;
    }

    /**
     * Get enrichColumnName
     *
     * @return string 
     */
    public function getEnrichColumnName()
    {
        return $this->enrichColumnName;
    }

    /**
     * Set enrichColumnNameSource
     *
     * @param string $enrichColumnNameSource
     * @return Operation
     */
    public function setEnrichColumnNameSource($enrichColumnNameSource)
    {
        $this->enrichColumnNameSource = $enrichColumnNameSource;

        return $this;
    }

    /**
     * Get enrichColumnNameSource
     *
     * @return string 
     */
    public function getEnrichColumnNameSource()
    {
        return $this->enrichColumnNameSource;
    }

    /**
     * Set enrichColumnNameRuleSource
     *
     * @param string $enrichColumnNameRuleSource
     * @return Operation
     */
    public function setEnrichColumnNameRuleSource($enrichColumnNameRuleSource)
    {
        $this->enrichColumnNameRuleSource = $enrichColumnNameRuleSource;

        return $this;
    }

    /**
     * Get enrichColumnNameRuleSource
     *
     * @return string 
     */
    public function getEnrichColumnNameRuleSource()
    {
        return $this->enrichColumnNameRuleSource;
    }

    /**
     * Set enrichRule
     *
     * @param string $enrichRule
     * @return Operation
     */
    public function setEnrichRule($enrichRule)
    {
        $this->enrichRule = $enrichRule;

        return $this;
    }

    /**
     * Get enrichRule
     *
     * @return string 
     */
    public function getEnrichRule()
    {
        return $this->enrichRule;
    }

    /**
     * Set enrichColumnFilter
     *
     * @param string $enrichColumnFilter
     * @return Operation
     */
    public function setEnrichColumnFilter($enrichColumnFilter)
    {
        $this->enrichColumnFilter = $enrichColumnFilter;

        return $this;
    }

    /**
     * Get enrichColumnFilter
     *
     * @return string 
     */
    public function getEnrichColumnFilter()
    {
        return $this->enrichColumnFilter;
    }

    /**
     * Set enrichValue
     *
     * @param string $enrichValue
     * @return Operation
     */
    public function setEnrichValue($enrichValue)
    {
        $this->enrichValue = $enrichValue;

        return $this;
    }

    /**
     * Get enrichValue
     *
     * @return string 
     */
    public function getEnrichValue()
    {
        return $this->enrichValue;
    }

    /**
     * Set enrichEgal
     *
     * @param string $enrichEgal
     * @return Operation
     */
    public function setEnrichEgal($enrichEgal)
    {
        $this->enrichEgal = $enrichEgal;

        return $this;
    }

    /**
     * Get enrichEgal
     *
     * @return string 
     */
    public function getEnrichEgal()
    {
        return $this->enrichEgal;
    }

    /**
     * Set enrichDifferent
     *
     * @param string $enrichDifferent
     * @return Operation
     */
    public function setEnrichDifferent($enrichDifferent)
    {
        $this->enrichDifferent = $enrichDifferent;

        return $this;
    }

    /**
     * Get enrichDifferent
     *
     * @return string 
     */
    public function getEnrichDifferent()
    {
        return $this->enrichDifferent;
    }

    /**
     * Set enrichSuperiorOrEgal
     *
     * @param string $enrichSuperiorOrEgal
     * @return Operation
     */
    public function setEnrichSuperiorOrEgal($enrichSuperiorOrEgal)
    {
        $this->enrichSuperiorOrEgal = $enrichSuperiorOrEgal;

        return $this;
    }

    /**
     * Get enrichSuperiorOrEgal
     *
     * @return string 
     */
    public function getEnrichSuperiorOrEgal()
    {
        return $this->enrichSuperiorOrEgal;
    }

    /**
     * Set enrichInferiorOrEgal
     *
     * @param string $enrichInferiorOrEgal
     * @return Operation
     */
    public function setEnrichInferiorOrEgal($enrichInferiorOrEgal)
    {
        $this->enrichInferiorOrEgal = $enrichInferiorOrEgal;

        return $this;
    }

    /**
     * Get enrichInferiorOrEgal
     *
     * @return string 
     */
    public function getEnrichInferiorOrEgal()
    {
        return $this->enrichInferiorOrEgal;
    }

    /**
     * Set enrichIncludingMin
     *
     * @param string $enrichIncludingMin
     * @return Operation
     */
    public function setEnrichIncludingMin($enrichIncludingMin)
    {
        $this->enrichIncludingMin = $enrichIncludingMin;

        return $this;
    }

    /**
     * Get enrichIncludingMin
     *
     * @return string 
     */
    public function getEnrichIncludingMin()
    {
        return $this->enrichIncludingMin;
    }

    /**
     * Set enrichIncludingMax
     *
     * @param string $enrichIncludingMax
     * @return Operation
     */
    public function setEnrichIncludingMax($enrichIncludingMax)
    {
        $this->enrichIncludingMax = $enrichIncludingMax;

        return $this;
    }

    /**
     * Get enrichIncludingMax
     *
     * @return string 
     */
    public function getEnrichIncludingMax()
    {
        return $this->enrichIncludingMax;
    }

    /**
     * Set publishProtocol
     *
     * @param string $publishProtocol
     * @return Operation
     */
    public function setPublishProtocol($publishProtocol)
    {
        $this->publishProtocol = $publishProtocol;

        return $this;
    }

    /**
     * Get publishProtocol
     *
     * @return string 
     */
    public function getPublishProtocol()
    {
        return $this->publishProtocol;
    }

    /**
     * Set publishHost
     *
     * @param string $publishHost
     * @return Operation
     */
    public function setPublishHost($publishHost)
    {
        $this->publishHost = $publishHost;

        return $this;
    }

    /**
     * Get publishHost
     *
     * @return string 
     */
    public function getPublishHost()
    {
        return $this->publishHost;
    }

    /**
     * Set publishPort
     *
     * @param integer $publishPort
     * @return Operation
     */
    public function setPublishPort($publishPort)
    {
        $this->publishPort = $publishPort;

        return $this;
    }

    /**
     * Get publishPort
     *
     * @return integer 
     */
    public function getPublishPort()
    {
        return $this->publishPort;
    }

    /**
     * Set publishLogin
     *
     * @param string $publishLogin
     * @return Operation
     */
    public function setPublishLogin($publishLogin)
    {
        $this->publishLogin = $publishLogin;

        return $this;
    }

    /**
     * Get publishLogin
     *
     * @return string 
     */
    public function getPublishLogin()
    {
        return $this->publishLogin;
    }

    /**
     * Set publishPassword
     *
     * @param string $publishPassword
     * @return Operation
     */
    public function setPublishPassword($publishPassword)
    {
        $this->publishPassword = $publishPassword;

        return $this;
    }

    /**
     * Get publishPassword
     *
     * @return string 
     */
    public function getPublishPassword()
    {
        return $this->publishPassword;
    }

    /**
     * Set publishDirectory
     *
     * @param string $publishDirectory
     * @return Operation
     */
    public function setPublishDirectory($publishDirectory)
    {
        $this->publishDirectory = $publishDirectory;

        return $this;
    }

    /**
     * Get publishDirectory
     *
     * @return string 
     */
    public function getPublishDirectory()
    {
        return $this->publishDirectory;
    }

    /**
     * Set publishFileName
     *
     * @param string $publishFileName
     * @return Operation
     */
    public function setPublishFileName($publishFileName)
    {
        $this->publishFileName = $publishFileName;

        return $this;
    }

    /**
     * Get publishFileName
     *
     * @return string 
     */
    public function getPublishFileName()
    {
        return $this->publishFileName;
    }

    /**
     * Set columnToDelete
     *
     * @param string $columnToDelete
     * @return Operation
     */
    public function setColumnToDelete($columnToDelete)
    {
        $this->columnToDelete = $columnToDelete;

        return $this;
    }

    /**
     * Get columnToDelete
     *
     * @return string 
     */
    public function getColumnToDelete()
    {
        return $this->columnToDelete;
    }

    /**
     * Set nameColumnPosition
     *
     * @param string $nameColumnPosition
     * @return Operation
     */
    public function setNameColumnPosition($nameColumnPosition)
    {
        $this->nameColumnPosition = $nameColumnPosition;

        return $this;
    }

    /**
     * Get nameColumnPosition
     *
     * @return string 
     */
    public function getNameColumnPosition()
    {
        return $this->nameColumnPosition;
    }

    /**
     * Set reorderPosition
     *
     * @param string $reorderPosition
     * @return Operation
     */
    public function setReorderPosition($reorderPosition)
    {
        $this->reorderPosition = $reorderPosition;

        return $this;
    }

    /**
     * Get reorderPosition
     *
     * @return string 
     */
    public function getReorderPosition()
    {
        return $this->reorderPosition;
    }

    /**
     * Set reorderColumnName
     *
     * @param string $reorderColumnName
     * @return Operation
     */
    public function setReorderColumnName($reorderColumnName)
    {
        $this->reorderColumnName = $reorderColumnName;

        return $this;
    }

    /**
     * Get reorderColumnName
     *
     * @return string 
     */
    public function getReorderColumnName()
    {
        return $this->reorderColumnName;
    }

    /**
     * Set json
     *
     * @param string $json
     * @return Operation
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set jsonCorrespondence
     *
     * @param string $jsonCorrespondence
     * @return Operation
     */
    public function setJsonCorrespondence($jsonCorrespondence)
    {
        $this->jsonCorrespondence = $jsonCorrespondence;

        return $this;
    }

    /**
     * Get jsonCorrespondence
     *
     * @return string 
     */
    public function getJsonCorrespondence()
    {
        return $this->jsonCorrespondence;
    }

    /**
     * Set columnsChecked
     *
     * @param array $columnsChecked
     * @return Operation
     */
    public function setColumnsChecked($columnsChecked)
    {
        $this->columnsChecked = $columnsChecked;

        return $this;
    }

    /**
     * Get columnsChecked
     *
     * @return array 
     */
    public function getColumnsChecked()
    {
        return $this->columnsChecked;
    }

    /**
     * Set nameColumnFormat
     *
     * @param string $nameColumnFormat
     * @return Operation
     */
    public function setNameColumnFormat($nameColumnFormat)
    {
        $this->nameColumnFormat = $nameColumnFormat;

        return $this;
    }

    /**
     * Get nameColumnFormat
     *
     * @return string 
     */
    public function getNameColumnFormat()
    {
        return $this->nameColumnFormat;
    }

    /**
     * Set columnFormat
     *
     * @param string $columnFormat
     * @return Operation
     */
    public function setColumnFormat($columnFormat)
    {
        $this->columnFormat = $columnFormat;

        return $this;
    }

    /**
     * Get columnFormat
     *
     * @return string 
     */
    public function getColumnFormat()
    {
        return $this->columnFormat;
    }

    /**
     * Set targetFormatPhone
     *
     * @param string $targetFormatPhone
     * @return Operation
     */
    public function setTargetFormatPhone($targetFormatPhone)
    {
        $this->targetFormatPhone = $targetFormatPhone;

        return $this;
    }

    /**
     * Get targetFormatPhone
     *
     * @return string 
     */
    public function getTargetFormatPhone()
    {
        return $this->targetFormatPhone;
    }

    /**
     * Set countryCode
     *
     * @param integer $countryCode
     * @return Operation
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return integer 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set newFormat
     *
     * @param string $newFormat
     * @return Operation
     */
    public function setNewFormat($newFormat)
    {
        $this->newFormat = $newFormat;

        return $this;
    }

    /**
     * Get newFormat
     *
     * @return string 
     */
    public function getNewFormat()
    {
        return $this->newFormat;
    }

    /**
     * Set sourceFormatDate
     *
     * @param string $sourceFormatDate
     * @return Operation
     */
    public function setSourceFormatDate($sourceFormatDate)
    {
        $this->sourceFormatDate = $sourceFormatDate;

        return $this;
    }

    /**
     * Get sourceFormatDate
     *
     * @return string 
     */
    public function getSourceFormatDate()
    {
        return $this->sourceFormatDate;
    }

    /**
     * Set targetFormatDate
     *
     * @param string $targetFormatDate
     * @return Operation
     */
    public function setTargetFormatDate($targetFormatDate)
    {
        $this->targetFormatDate = $targetFormatDate;

        return $this;
    }

    /**
     * Get targetFormatDate
     *
     * @return string 
     */
    public function getTargetFormatDate()
    {
        return $this->targetFormatDate;
    }

    /**
     * Set targetColumn
     *
     * @param string $targetColumn
     * @return Operation
     */
    public function setTargetColumn($targetColumn)
    {
        $this->targetColumn = $targetColumn;

        return $this;
    }

    /**
     * Get targetColumn
     *
     * @return string 
     */
    public function getTargetColumn()
    {
        return $this->targetColumn;
    }

    /**
     * Set filterManualColumns
     *
     * @param array $filterManualColumns
     * @return Operation
     */
    public function setFilterManualColumns($filterManualColumns)
    {
        $this->filterManualColumns = $filterManualColumns;

        return $this;
    }

    /**
     * Get filterManualColumns
     *
     * @return array 
     */
    public function getFilterManualColumns()
    {
        return $this->filterManualColumns;
    }

    /**
     * Set dealing
     *
     * @param \Satisfactory\OperationBundle\Entity\Dealing $dealing
     * @return Operation
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

    /**
     * Set createdBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $createdBy
     * @return Operation
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
     * Set correspondance
     *
     * @param \Satisfactory\OperationBundle\Entity\Correspondance $correspondance
     * @return Operation
     */
    public function setCorrespondance(\Satisfactory\OperationBundle\Entity\Correspondance $correspondance = null)
    {
        $this->correspondance = $correspondance;

        return $this;
    }

    /**
     * Get correspondance
     *
     * @return \Satisfactory\OperationBundle\Entity\Correspondance 
     */
    public function getCorrespondance()
    {
        return $this->correspondance;
    }

    /**
     * Set reject
     *
     * @param \Satisfactory\OperationBundle\Entity\Reject $reject
     * @return Operation
     */
    public function setReject(\Satisfactory\OperationBundle\Entity\Reject $reject = null)
    {
        $this->reject = $reject;

        return $this;
    }

    /**
     * Get reject
     *
     * @return \Satisfactory\OperationBundle\Entity\Reject 
     */
    public function getReject()
    {
        return $this->reject;
    }

    /**
     * Set filtering
     *
     * @param \Satisfactory\OperationBundle\Entity\Filtering $filtering
     * @return Operation
     */
    public function setFiltering(\Satisfactory\OperationBundle\Entity\Filtering $filtering = null)
    {
        $this->filtering = $filtering;

        return $this;
    }

    /**
     * Get filtering
     *
     * @return \Satisfactory\OperationBundle\Entity\Filtering 
     */
    public function getFiltering()
    {
        return $this->filtering;
    }

    /**
     * Add cronexecutionOperations
     *
     * @param \Satisfactory\CronBundle\Entity\CronexecutionOperation $cronexecutionOperations
     * @return Operation
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
     * Set replaceName
     *
     * @param string $replaceName
     * @return Operation
     */
    public function setReplaceName($replaceName)
    {
        $this->replaceName = $replaceName;

        return $this;
    }

    /**
     * Get replaceName
     *
     * @return string 
     */
    public function getReplaceName()
    {
        return $this->replaceName;
    }

    /**
     * Set replaceColumns
     *
     * @param string $replaceColumns
     * @return Operation
     */
    public function setReplaceColumns($replaceColumns)
    {
        $this->replaceColumns = $replaceColumns;

        return $this;
    }

    /**
     * Get replaceColumns
     *
     * @return string 
     */
    public function getReplaceColumns()
    {
        return $this->replaceColumns;
    }

    /**
     * Set replaceUppercase
     *
     * @param boolean $replaceUppercase
     * @return Operation
     */
    public function setReplaceUppercase($replaceUppercase)
    {
        $this->replaceUppercase = $replaceUppercase;

        return $this;
    }

    /**
     * Get replaceUppercase
     *
     * @return boolean 
     */
    public function getReplaceUppercase()
    {
        return $this->replaceUppercase;
    }

    /**
     * Set replaceLowercase
     *
     * @param boolean $replaceLowercase
     * @return Operation
     */
    public function setReplaceLowercase($replaceLowercase)
    {
        $this->replaceLowercase = $replaceLowercase;

        return $this;
    }

    /**
     * Get replaceLowercase
     *
     * @return boolean 
     */
    public function getReplaceLowercase()
    {
        return $this->replaceLowercase;
    }

    /**
     * Set replaceCapitalize
     *
     * @param boolean $replaceCapitalize
     * @return Operation
     */
    public function setReplaceCapitalize($replaceCapitalize)
    {
        $this->replaceCapitalize = $replaceCapitalize;

        return $this;
    }

    /**
     * Get replaceCapitalize
     *
     * @return boolean 
     */
    public function getReplaceCapitalize()
    {
        return $this->replaceCapitalize;
    }

    /**
     * Set replaceModel
     *
     * @param boolean $replaceModel
     * @return Operation
     */
    public function setReplaceModel($replaceModel)
    {
        $this->replaceModel = $replaceModel;

        return $this;
    }

    /**
     * Get replaceModel
     *
     * @return boolean 
     */
    public function getReplaceModel()
    {
        return $this->replaceModel;
    }

    /**
     * Set replaceModelFormat
     *
     * @param string $replaceModelFormat
     * @return Operation
     */
    public function setReplaceModelFormat($replaceModelFormat)
    {
        $this->replaceModelFormat = $replaceModelFormat;

        return $this;
    }

    /**
     * Get replaceModelFormat
     *
     * @return string 
     */
    public function getReplaceModelFormat()
    {
        return $this->replaceModelFormat;
    }

    /**
     * Set replaceReplacement
     *
     * @param string $replaceReplacement
     * @return Operation
     */
    public function setReplaceReplacement($replaceReplacement)
    {
        $this->replaceReplacement = $replaceReplacement;

        return $this;
    }

    /**
     * Get replaceReplacement
     *
     * @return string 
     */
    public function getReplaceReplacement()
    {
        return $this->replaceReplacement;
    }

    /**
     * Set replaceReplacementFormat
     *
     * @param string $replaceReplacementFormat
     * @return Operation
     */
    public function setReplaceReplacementFormat($replaceReplacementFormat)
    {
        $this->replaceReplacementFormat = $replaceReplacementFormat;

        return $this;
    }

    /**
     * Get replaceReplacementFormat
     *
     * @return string 
     */
    public function getReplaceReplacementFormat()
    {
        return $this->replaceReplacementFormat;
    }

    /**
     * Set replaceFullCell
     *
     * @param boolean $replaceFullCell
     * @return Operation
     */
    public function setReplaceFullCell($replaceFullCell)
    {
        $this->replaceFullCell = $replaceFullCell;

        return $this;
    }

    /**
     * Get replaceFullCell
     *
     * @return boolean 
     */
    public function getReplaceFullCell()
    {
        return $this->replaceFullCell;
    }

    /**
     * Set replacePartOfCell
     *
     * @param boolean $replacePartOfCell
     * @return Operation
     */
    public function setReplacePartOfCell($replacePartOfCell)
    {
        $this->replacePartOfCell = $replacePartOfCell;

        return $this;
    }

    /**
     * Get replacePartOfCell
     *
     * @return boolean 
     */
    public function getReplacePartOfCell()
    {
        return $this->replacePartOfCell;
    }
}
