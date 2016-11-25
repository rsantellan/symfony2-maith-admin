<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of mAlbum
 *
 * @ORM\Table(name="maith_album")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Maith\Common\AdminBundle\Entity\mAlbumRepository")
 * @author Rodrigo Santellan
 */
class mAlbum {

  const ALBUM_AVATAR_CACHE_KEY = 'album_avatar_';
  
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
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;
  
  /**
   * @var string
   *
   * @ORM\Column(name="object_id", type="string", length=255)
   */
  private $object_id;
  
  /**
   * @var string
   *
   * @ORM\Column(name="object_class", type="string", length=255)
   */
  private $object_class;

  /**
  * @var boolean
  *
  * @ORM\Column(name="hasonlinevideo", type="boolean", nullable=true)
  */
  private $hasonlinevideo = false;
  
  /**
   * var Projects
   * @ORM\OneToMany(targetEntity="mFile", mappedBy="album", cascade={"remove"})
   * @ORM\OrderBy({"orden" = "ASC"})
   */
  private $files;  

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
     * @return mAlbum
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
     * Set object_id
     *
     * @param string $objectId
     * @return mAlbum
     */
    public function setObjectId($objectId)
    {
        $this->object_id = $objectId;
    
        return $this;
    }

    /**
     * Get object_id
     *
     * @return string 
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * Set object_class
     *
     * @param string $objectClass
     * @return mAlbum
     */
    public function setObjectClass($objectClass)
    {
        $this->object_class = $objectClass;
    
        return $this;
    }

    /**
     * Get object_class
     *
     * @return string 
     */
    public function getObjectClass()
    {
        return $this->object_class;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add files
     *
     * @param \Maith\Common\AdminBundle\Entity\mFile $files
     * @return mAlbum
     */
    public function addFile(\Maith\Common\AdminBundle\Entity\mFile $files)
    {
        $this->files[] = $files;
    
        return $this;
    }

    /**
     * Remove files
     *
     * @param \Maith\Common\AdminBundle\Entity\mFile $files
     */
    public function removeFile(\Maith\Common\AdminBundle\Entity\mFile $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
    
    public function getHasonlinevideo() {
      return $this->hasonlinevideo;
    }

    public function setHasonlinevideo($hasonlinevideo) {
      $this->hasonlinevideo = $hasonlinevideo;
    }


    public function retrieveAvatarCacheKey($hashed = true){
      $data = $this->getName().$this->getObjectClass().$this->getObjectId();
      if($hashed){
        return md5($data);  
      }
      return $data;
    }
}