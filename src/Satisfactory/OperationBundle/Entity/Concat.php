<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_concat")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\ConcatRepository")
 */
class Concat
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
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;
    
    /**
     * @ORM\ManyToOne("Satisfactory\OperationBundle\Entity\Operation", inversedBy="concat", cascade={"persist"})
     * @ORM\JoinColumn(name="operation", referencedColumnName="id", onDelete="CASCADE")
     */
    private $operation;
    
    /**
     * @var integer $concatProtocol
     *
     * @ORM\Column(name="concatProtocol", type="string")
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatProtocol;
    
    /**
     * @var string $concatHost
     *
     * @ORM\Column(name="concatHost", type="string", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatHost;
    
    /**
     *
     * @ORM\Column(name="concatPort", type="integer", nullable=false)
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatPort;
    
    /**
     * @var string $concatLogin
     *
     * @ORM\Column(name="concatLogin", type="string")
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatLogin;
    
    /**
     * @var string $concatPassword
     *
     * @ORM\Column(name="concatPassword", type="string")
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatPassword;
    
    /**
     * @var string $concatDirectory
     *
     * @ORM\Column(name="concatDirectory", type="string")
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatDirectory;
    
    /**
     * @var string $concatFilename
     *
     * @ORM\Column(name="concatFilename", type="string")
     * @Assert\NotBlank(message="Champ obligatoire ! ")
     */
    private $concatFilename;
    
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
}
