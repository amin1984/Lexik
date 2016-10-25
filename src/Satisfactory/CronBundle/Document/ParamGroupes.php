<?php

namespace Satisfactory\CronBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Satisfactory\CronBundle\Document\ParamGroupes
 * 
 * @ODM\Document
 * @ODM\Document(repositoryClass="Satisfactory\CronBundle\Repository\ParamGroupesRepository")
 * @ODM\ChangeTrackingPolicy("DEFERRED_IMPLICIT")  
 */
class ParamGroupes
{
    /**
     * @var MongoId $id
     *
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var integer $index
     *
     * @ODM\Field(name="index", type="integer")
     */
    protected $index;
    
    /**
     * @var string $name
     *
     * @ODM\Field(name="name", type="string")
     */
    protected $name;

    /**
     * @var integer $agency
     *
     * @ODM\Field(name="agency", type="integer")
     */
    protected $agency;


    /**
     * @var integer quest
     *
     * @ODM\Field(name="quest", type="integer")
     */
    protected $quest;

    /**
     * @var integer segment
     *
     * @ODM\Field(name="segment", type="integer")
     */
    protected $segment;

    /**
     * @var integer groupId
     *
     * @ODM\Field(name="groupId", type="integer")
     */
    protected $groupId;

    /**
     * @var integer userId
     *
     * @ODM\Field(name="userId", type="integer")
     */
    protected $userId;

    /**
     * @var string $beginAt
     *
     * @ODM\Field(name="beginAt", type="string")
     */
    protected $beginAt;
    
    /**
     * @var string $endAt
     *
     * @ODM\Field(name="endAt", type="string")
     */
    protected $endAt;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set index
     *
     * @param integer $index
     * @return self
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Get index
     *
     * @return integer $index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set agency
     *
     * @param integer $agency
     * @return self
     */
    public function setAgency($agency)
    {
        $this->agency = $agency;
        return $this;
    }

    /**
     * Get agency
     *
     * @return integer $agency
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Set quest
     *
     * @param integer $quest
     * @return self
     */
    public function setQuest($quest)
    {
        $this->quest = $quest;
        return $this;
    }

    /**
     * Get quest
     *
     * @return integer $quest
     */
    public function getQuest()
    {
        return $this->quest;
    }

    /**
     * Set segment
     *
     * @param integer $segment
     * @return self
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
        return $this;
    }

    /**
     * Get segment
     *
     * @return integer $segment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer $groupId
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set beginAt
     *
     * @param string $beginAt
     * @return self
     */
    public function setBeginAt($beginAt)
    {
        $this->beginAt = $beginAt;
        return $this;
    }

    /**
     * Get beginAt
     *
     * @return string $beginAt
     */
    public function getBeginAt()
    {
        return $this->beginAt;
    }

    /**
     * Set endAt
     *
     * @param string $endAt
     * @return self
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * Get endAt
     *
     * @return string $endAt
     */
    public function getEndAt()
    {
        return $this->endAt;
    }
}
