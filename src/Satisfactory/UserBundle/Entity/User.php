<?php

namespace Satisfactory\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="satisfactory_user")
 * @ORM\Entity(repositoryClass="Satisfactory\UserBundle\Repository\UserRepository")
 * @UniqueEntity("sharingData", message="Cette valeur est déja utilisée!")
 * @UniqueEntity("username", message="Cette valeur est déja utilisée!")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="text", nullable=true)
     */
    private $picture;
    
     /**
     * @var string
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    protected $username;
    
     /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;
    
    /**
     * @var array
     */
    protected $roles;
    
    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;
    
    /**
     * @var boolean
     */
    protected $enabled;
    
     /**
     * @var string
     *
     * @ORM\Column(name="sharing_data", type="string", nullable=false, unique=true)
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $sharingData;
    
    /**
     * @var User $createdBy
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var User $updatedBy
     *
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User")
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
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Dealing", mappedBy="client", cascade={"remove","persist"})
     */
    private $dealing;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Reject", mappedBy="client", cascade={"remove","persist"})
     */
    private $reject;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Satisfactory\OperationBundle\Entity\Correspondance", mappedBy="client", cascade={"remove","persist"})
     */
    private $correspondance;
    
    /**
     * @var string
     *
     * @ORM\Column(name="creator_name", type="text", nullable=true)
     */
    private $creatorName;
    
     public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime();
        $this->password = 0000;
        $this->setSharingData('S-D');
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
     * Set picture
     *
     * @param string $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set sharingData
     *
     * @param string $sharingData
     * @return User
     */
    public function setSharingData($sharingData)
    {
        $this->sharingData = $sharingData;

        return $this;
    }

    /**
     * Get sharingData
     *
     * @return string 
     */
    public function getSharingData()
    {
        return $this->sharingData;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Set name
     *
     * @param string $name
     * @return User
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
     * Get email
     *
     * @return string 
     */
    
    public function getEmail()
    {
        $this->email = $this->username."@groupama.com";
        return $this->email;
    }
    
    public function __toString() 
    {
        return $this->getLastName()." ".$this->getFirstName();
    }


    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Add dealing
     *
     * @param \Satisfactory\OperationBundle\Entity\Dealing $dealing
     * @return User
     */
    public function addDealing(\Satisfactory\OperationBundle\Entity\Dealing $dealing)
    {
        $this->dealing[] = $dealing;

        return $this;
    }

    /**
     * Remove dealing
     *
     * @param \Satisfactory\OperationBundle\Entity\Dealing $dealing
     */
    public function removeDealing(\Satisfactory\OperationBundle\Entity\Dealing $dealing)
    {
        $this->dealing->removeElement($dealing);
    }

    /**
     * Get dealing
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDealing()
    {
        return $this->dealing;
    }

    /**
     * Add correspondance
     *
     * @param \Satisfactory\OperationBundle\Entity\Correspondance $correspondance
     * @return User
     */
    public function addCorrespondance(\Satisfactory\OperationBundle\Entity\Correspondance $correspondance)
    {
        $this->correspondance[] = $correspondance;

        return $this;
    }

    /**
     * Remove correspondance
     *
     * @param \Satisfactory\OperationBundle\Entity\Correspondance $correspondance
     */
    public function removeCorrespondance(\Satisfactory\OperationBundle\Entity\Correspondance $correspondance)
    {
        $this->correspondance->removeElement($correspondance);
    }

    /**
     * Get correspondance
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCorrespondance()
    {
        return $this->correspondance;
    }

    /**
     * Set creatorName
     *
     * @param string $creatorName
     * @return User
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
     * Add reject
     *
     * @param \Satisfactory\OperationBundle\Entity\Reject $reject
     * @return User
     */
    public function addReject(\Satisfactory\OperationBundle\Entity\Reject $reject)
    {
        $this->reject[] = $reject;

        return $this;
    }

    /**
     * Remove reject
     *
     * @param \Satisfactory\OperationBundle\Entity\Reject $reject
     */
    public function removeReject(\Satisfactory\OperationBundle\Entity\Reject $reject)
    {
        $this->reject->removeElement($reject);
    }

    /**
     * Get reject
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReject()
    {
        return $this->reject;
    }
}
