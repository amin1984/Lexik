<?php

namespace Satisfactory\SettingBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Setting
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="Enregistrement impossible car ce paramétrage existe déjà pour l'agence ou certaines des agences sélectionnée(s)."
 * )
 * @ORM\Table(name="satisfactory_setting")
 * @ORM\Entity(repositoryClass="Satisfactory\SettingBundle\Repository\SettingRepository")
 */
class Setting {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Assert\NotBlank(message="Veuillez saisir un nom pour votre paramétrage")
     */
    private $name;

    /**
     * @ORM\ManyToOne("Satisfactory\SettingBundle\Entity\Quest", cascade={"persist"})
     * @ORM\JoinColumn(name="quest", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Veuillez sélectionner un type d'enquête")
     */
    private $quest;
    
    /**
     * @ORM\ManyToMany("Satisfactory\SettingBundle\Entity\Agency", cascade={"persist"})
     * @ORM\JoinColumn(name="agency", referencedColumnName="id", nullable=false)
     */
    private $agency;
    
    /**
     * @ORM\ManyToMany("Satisfactory\SettingBundle\Entity\Segment", cascade={"persist"})
     * @ORM\JoinColumn(name="segment", referencedColumnName="id", nullable=false)
     */
    private $segment;
    
    /**
     * @ORM\ManyToOne("Satisfactory\UserBundle\Entity\User", inversedBy="setting", cascade={"persist"})
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $status;
    
    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $groupId;
    
    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="createdBy", type="string", nullable=true)
     */
    private $createdBy;
    
    
    /**
     * @ORM\Column(name="dateBegin", type="datetime", nullable=true)
     * @Assert\NotBlank(message="Veuillez sélectionner une date de début")
     */
    private $dateBegin;
    
    /**
     * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
     * @Assert\NotBlank(message="Veuillez sélectionner une date de fin") 
     */
    private $dateEnd;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agency = new \Doctrine\Common\Collections\ArrayCollection();
        $this->segment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Setting
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
     * Set groupId
     *
     * @param integer $groupId
     * @return Setting
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Setting
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
     * Set dateBegin
     *
     * @param \DateTime $dateBegin
     * @return Setting
     */
    public function setDateBegin($dateBegin)
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin
     *
     * @return \DateTime 
     */
    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return Setting
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set quest
     *
     * @param \Satisfactory\SettingBundle\Entity\Quest $quest
     * @return Setting
     */
    public function setQuest(\Satisfactory\SettingBundle\Entity\Quest $quest)
    {
        $this->quest = $quest;

        return $this;
    }

    /**
     * Get quest
     *
     * @return \Satisfactory\SettingBundle\Entity\Quest 
     */
    public function getQuest()
    {
        return $this->quest;
    }

    /**
     * Add agency
     *
     * @param \Satisfactory\SettingBundle\Entity\Agency $agency
     * @return Setting
     */
    public function addAgency(\Satisfactory\SettingBundle\Entity\Agency $agency)
    {
        $this->agency[] = $agency;

        return $this;
    }

    /**
     * Remove agency
     *
     * @param \Satisfactory\SettingBundle\Entity\Agency $agency
     */
    public function removeAgency(\Satisfactory\SettingBundle\Entity\Agency $agency)
    {
        $this->agency->removeElement($agency);
    }

    /**
     * Get agency
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Add segment
     *
     * @param \Satisfactory\SettingBundle\Entity\Segment $segment
     * @return Setting
     */
    public function addSegment(\Satisfactory\SettingBundle\Entity\Segment $segment)
    {
        $this->segment[] = $segment;

        return $this;
    }

    /**
     * Remove segment
     *
     * @param \Satisfactory\SettingBundle\Entity\Segment $segment
     */
    public function removeSegment(\Satisfactory\SettingBundle\Entity\Segment $segment)
    {
        $this->segment->removeElement($segment);
    }

    /**
     * Get segment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Set user
     *
     * @param \Satisfactory\UserBundle\Entity\User $user
     * @return Setting
     */
    public function setUser(\Satisfactory\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Satisfactory\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Setting
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Setting
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
     * @param string $createdBy
     * @return Setting
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
