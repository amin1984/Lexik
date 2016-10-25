<?php

namespace Satisfactory\SettingBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Segment
 *
 * @ORM\Table(name="satisfactory_segment")
 * @ORM\Entity(repositoryClass="Satisfactory\SettingBundle\Repository\SegmentRepository")
 */
class Segment {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $segment_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var file $file
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire ! ")
     */
    private $file;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Upload file
     */
    public function getAbsolutePath() {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath() {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    public function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/settings/segment';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
        }
//        else {
        $this->path = null;
        $this->path = $this->file->getBasename() . "_" . $this->file->getClientOriginalName();
//        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * Get separator from CSV string
     */
    protected function get_separator($csvstring, $fallback = ';') {
        $seps = array(';', ',', '|', "\t");
        $max = 0;
        $separator = false;
        foreach ($seps as $sep) {
            $count = substr_count($csvstring, $sep);
            if ($count > $max) {
                $separator = $sep;
                $max = $count;
            }
        }

        if ($separator)
            return $separator;
        return $fallback;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
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
            @unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * Control of file extension and content
     */
    public function checkFile() {
        $fileContent = file_get_contents($this->getFile());
        $separator = $this->get_separator($fileContent, ";");
        // CSV format
        $tab = explode('.',$this->getFile()->getClientOriginalName());
        if ( $tab[1] == 'csv' ) {
            // ; separator
            if ($separator == ";") {
                // Columns of file
                $line = explode(PHP_EOL, $fileContent);
                $line[0] = preg_replace( "/\r|\n/", "", $line[0] );
                if ($line[0] == "id_segment;nom_segment") {
                    $path = 'uploads/settings/segment';
                    // Permission to directory
                    if (!is_dir($path))
                        mkdir($path, 0755, true);
                    $rep = opendir($path);

                    // Array of User
                    $tabUser = array();
                    $cpt = 0;
                    $redundancy = false;
                    for ($i = 1; $i < count($line); $i++) {
                        $tab = explode(';', $line[$i]);
                        for ($j = 0; $j < count($tab); $j++) {
                            if ($j % 2 == 0) {
                                if ($tab[$j]) {

                                    if ($this->redundancy($tab[$j], $tabUser))
                                        $redundancy = true;

                                    $tabUser[$cpt] = $tab[$j];
                                    $cpt++;
                                }
                            }
                        }
                    }
                    // Redundancy in array
                    if (!$redundancy) {
                        while ($file = readdir($rep)) {
                            if ($file != '..' && $file != '.' && $file != '' && $file != '.htaccess') {
                                unlink($path . "/" . $file);
                            }
                        }
                    } else {
                        return 'Le fichier contient un doublon sur la base de "id_segment" !';
                    }
                } else {
                    return 'Le format du fichier est incorrecte !';
                }
            } else {
                return 'Le séparateur de fichier csv doit être obligatoirement ";"';
            }
        } else {
            return 'Le fichier doit être un fichier au format csv !';
        }
    }

    /**
     * Redundancy in array
     * return boolean
     */
    protected function redundancy($valeur, $array) {
        $i = 0;
        foreach ($array AS $ligne) {
            if ($ligne == $valeur) {
                $i++;
            }
        }
        if ($i > 0)
            return true;
        return false;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }


    /**
     * Set segment_id
     *
     * @param integer $segmentId
     * @return Segment
     */
    public function setSegmentId($segmentId)
    {
        $this->segment_id = $segmentId;

        return $this;
    }

    /**
     * Get segment_id
     *
     * @return integer 
     */
    public function getSegmentId()
    {
        return $this->segment_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Segment
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
     * Set path
     *
     * @param string $path
     * @return Segment
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
     * Get file
     *
     * @return string 
     */
    public function getFile() {
        return $this->file;
    }
    
    public function __toString() {
        return $this->getName();
    }
}
