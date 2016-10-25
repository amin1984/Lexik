<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_replacement")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\ReplacementRepository")
 */
class Replacement
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
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Operation", inversedBy="concat", cascade={"persist"})
     * @ORM\JoinColumn(name="operation", referencedColumnName="id", onDelete="CASCADE")
     */
    private $operation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="replaceName", type="string", nullable=false)
     */
    private $replaceName;
    
    /**
     * @var text
     *
     * @ORM\Column(name="replaceColumns", type="text", nullable=false)
     */
    private $replaceColumns;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="replaceUppercase", type="boolean", nullable=true)
     */
    private $replaceUppercase;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="replaceLowercase", type="boolean", nullable=true)
     */
    private $replaceLowercase;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="replaceCapitalize", type="boolean", nullable=true)
     */
    private $replaceCapitalize;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="replaceReplace", type="boolean", nullable=true)
     */
    private $replaceReplace;
    
    /**
     * @var string
     *
     * @ORM\Column(name="replaceStringToReplace", type="string", nullable=true)
     */
    private $replaceStringToReplace;
    
    /**
     * @var string
     *
     * @ORM\Column(name="replaceStringFormat", type="string", nullable=true)
     */
    private $replaceStringToReplaceFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="replaceReplacement", type="string", nullable=true)
     */
    private $replaceReplacement;
    
    /**
     * @var string
     *
     * @ORM\Column(name="replaceReplacementFormat", type="string", nullable=true)
     */
    private $replaceReplacementFormat;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;
    
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
     * Set status
     *
     * @param boolean $status
     * @return Concat
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
     * Set concatProtocol
     *
     * @param integer $concatProtocol
     * @return Concat
     */
    public function setConcatProtocol($concatProtocol)
    {
        $this->concatProtocol = $concatProtocol;

        return $this;
    }

    /**
     * Get concatProtocol
     *
     * @return integer 
     */
    public function getConcatProtocol()
    {
        return $this->concatProtocol;
    }

    /**
     * Set concatHost
     *
     * @param string $concatHost
     * @return Concat
     */
    public function setConcatHost($concatHost)
    {
        $this->concatHost = $concatHost;

        return $this;
    }

    /**
     * Get concatHost
     *
     * @return string 
     */
    public function getConcatHost()
    {
        return $this->concatHost;
    }

    /**
     * Set concatPort
     *
     * @param integer $concatPort
     * @return Concat
     */
    public function setConcatPort($concatPort)
    {
        $this->concatPort = $concatPort;

        return $this;
    }

    /**
     * Get concatPort
     *
     * @return integer 
     */
    public function getConcatPort()
    {
        return $this->concatPort;
    }

    /**
     * Set concatLogin
     *
     * @param string $concatLogin
     * @return Concat
     */
    public function setConcatLogin($concatLogin)
    {
        $this->concatLogin = $concatLogin;

        return $this;
    }

    /**
     * Get concatLogin
     *
     * @return string 
     */
    public function getConcatLogin()
    {
        return $this->concatLogin;
    }

    /**
     * Set concatPassword
     *
     * @param string $concatPassword
     * @return Concat
     */
    public function setConcatPassword($concatPassword)
    {
        $this->concatPassword = $concatPassword;

        return $this;
    }

    /**
     * Get concatPassword
     *
     * @return string 
     */
    public function getConcatPassword()
    {
        return $this->concatPassword;
    }

    /**
     * Set concatDirectory
     *
     * @param string $concatDirectory
     * @return Concat
     */
    public function setConcatDirectory($concatDirectory)
    {
        $this->concatDirectory = $concatDirectory;

        return $this;
    }

    /**
     * Get concatDirectory
     *
     * @return string 
     */
    public function getConcatDirectory()
    {
        return $this->concatDirectory;
    }

    /**
     * Set concatFilename
     *
     * @param string $concatFilename
     * @return Concat
     */
    public function setConcatFilename($concatFilename)
    {
        $this->concatFilename = $concatFilename;

        return $this;
    }

    /**
     * Get concatFilename
     *
     * @return string 
     */
    public function getConcatFilename()
    {
        return $this->concatFilename;
    }

    /**
     * Set operation
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operation
     * @return Concat
     */
    public function setOperation(\Satisfactory\OperationBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \Satisfactory\OperationBundle\Entity\Operation 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set replaceName
     *
     * @param string $replaceName
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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
     * @return Replacement
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

    /**
     * Set replaceReplace
     *
     * @param boolean $replaceReplace
     * @return Replacement
     */
    public function setReplaceReplace($replaceReplace)
    {
        $this->replaceReplace = $replaceReplace;

        return $this;
    }

    /**
     * Get replaceReplace
     *
     * @return boolean 
     */
    public function getReplaceReplace()
    {
        return $this->replaceReplace;
    }

    /**
     * Set replaceStringToReplace
     *
     * @param string $replaceStringToReplace
     * @return Replacement
     */
    public function setReplaceStringToReplace($replaceStringToReplace)
    {
        $this->replaceStringToReplace = $replaceStringToReplace;

        return $this;
    }

    /**
     * Get replaceStringToReplace
     *
     * @return string 
     */
    public function getReplaceStringToReplace()
    {
        return $this->replaceStringToReplace;
    }

    /**
     * Set replaceStringToReplaceFormat
     *
     * @param string $replaceStringToReplaceFormat
     * @return Replacement
     */
    public function setReplaceStringToReplaceFormat($replaceStringToReplaceFormat)
    {
        $this->replaceStringToReplaceFormat = $replaceStringToReplaceFormat;

        return $this;
    }

    /**
     * Get replaceStringToReplaceFormat
     *
     * @return string 
     */
    public function getReplaceStringToReplaceFormat()
    {
        return $this->replaceStringToReplaceFormat;
    }
}
