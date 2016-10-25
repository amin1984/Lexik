<?php

namespace Satisfactory\OperationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_correspondance")
 * @ORM\Entity(repositoryClass="Satisfactory\OperationBundle\Repository\CorrespondanceRepository")
 */
class Correspondance
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
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $name;
    
    /**
     * @var User $client
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", inversedBy="correspondance", cascade={"persist"})
     * @ORM\JoinColumn(name="client", referencedColumnName="id", nullable=false)
     */
    private $client;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;
    
    /**
     * @var file $file
     * 
     * @Assert\File( maxSize = "3000000",
     * maxSizeMessage = "Veuillez svp faire une demande au service informatique pour un ajout manuel.",
     * mimeTypes= {"text/plain", "text/csv", "application/csv", "text/excel", "application/excel"}, 
     * mimeTypesMessage = "Le fichier doit être un fichier au format csv")
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $file;
    
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
     * @var boolean
     *
     * @ORM\Column(name="replacing", type="boolean")
     */
    private $replacing;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isRegEx", type="boolean")
     */
    private $isRegEx;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="columnNumber", type="integer")
     */
    private $columnNumber;
    
    /**
     * @var array
     *
     * @ORM\Column(name="columns", type="array")
     */
    private $columns;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Operation", mappedBy="dealing", cascade={"persist"})
     */
    private $operations;
    
    /**
     * To string
     * 
     */
    public function __toString() 
    {
        return $this->getName();
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
     * @return Correspondance
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
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set client
     *
     * @param \Satisfactory\UserBundle\Entity\User $client
     * @return Correspondance
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
     * Upload file
     */
     public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
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
        return 'uploads/correspondances';
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
        } 
//        else {
            $this->path = null;
        $this->path = $this->file->getBasename()."_".$this->file->getClientOriginalName();
//        }
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
            @unlink($this->getUploadRootDir().'/'.$this->temp);
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Correspondance
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
     * Set createdBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $createdBy
     * @return Correspondance
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
     * Set path
     *
     * @param string $path
     * @return Correspondance
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Correspondance
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
     * Set updatedBy
     *
     * @param \Satisfactory\UserBundle\Entity\User $updatedBy
     * @return Correspondance
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
     * Set creatorName
     *
     * @param string $creatorName
     * @return Correspondance
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
     * Constructor
     */
    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add operations
     *
     * @param \Satisfactory\OperationBundle\Entity\Operation $operations
     * @return Correspondance
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
     * Set replacing
     *
     * @param boolean $replacing
     * @return Correspondance
     */
    public function setReplacing($replacing)
    {
        $this->replacing = $replacing;

        return $this;
    }

    /**
     * Get replacing
     *
     * @return boolean 
     */
    public function getReplacing()
    {
        return $this->replacing;
    }

    /**
     * Set isRegEx
     *
     * @param boolean $isRegEx
     * @return Correspondance
     */
    public function setIsRegEx($isRegEx)
    {
        $this->isRegEx = $isRegEx;

        return $this;
    }

    /**
     * Get isRegEx
     *
     * @return boolean 
     */
    public function getIsRegEx()
    {
        return $this->isRegEx;
    }

    /**
     * Set columnNumber
     *
     * @param integer $columnNumber
     * @return Correspondance
     */
    public function setColumnNumber($columnNumber)
    {
        $this->columnNumber = $columnNumber;

        return $this;
    }

    /**
     * Get columnNumber
     *
     * @return integer 
     */
    public function getColumnNumber()
    {
        return $this->columnNumber;
    }

    /**
     * Set columns
     *
     * @param array $columns
     * @return Correspondance
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns
     *
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }

}
