<?php

namespace Satisfactory\SettingBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Agency
 *
 * @ORM\Table(name="satisfactory_appParam")
 * @ORM\Entity(repositoryClass="Satisfactory\SettingBundle\Repository\AgencyRepository")
 */
class AppParam {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Email(
     *     message = "Cet email '{{ value }}' n'est pas un email valide.")
     * @ORM\Column(name="email", nullable=true)
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="idParamErdf", type="string", nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $idParamErdf;

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
     * Set email
     *
     * @param string $email
     * @return AppParam
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set idParamErdf
     *
     * @param string $idParamErdf
     * @return AppParam
     */
    public function setIdParamErdf($idParamErdf)
    {
        $this->idParamErdf = $idParamErdf;

        return $this;
    }

    /**
     * Get idParamErdf
     *
     * @return string 
     */
    public function getIdParamErdf()
    {
        return $this->idParamErdf;
    }
}
