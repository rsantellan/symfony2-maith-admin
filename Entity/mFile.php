<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Finder\Finder;

/**
 * Description of mFile
 * 
 * @ORM\Table(name="maith_file")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @author Rodrigo Santellan
 */
class mFile {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
      * @Gedmo\SortablePosition
      * @ORM\Column(type="integer")
      */
     protected $orden;
     
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="sf_path", type="string", length=255)
     */
    private $sfPath;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
      /**
      *
      * @ORM\ManyToOne(targetEntity="mAlbum", inversedBy="files")
      * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
      * 
      */
     protected $album;

    /**
     * @var string
     *
     * @ORM\Column(name="show_name", type="string", length=255)
     */
    private $showName = "";
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return mFile
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return mFile
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
     * @return mFile
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
     * Set type
     *
     * @param string $type
     * @return mFile
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
     * Set album
     *
     * @param \Maith\Common\AdminBundle\Entity\mAlbum $album
     * @return mFile
     */
    public function setAlbum(\Maith\Common\AdminBundle\Entity\mAlbum $album = null)
    {
        $this->album = $album;
    
        return $this;
    }

    /**
     * Get album
     *
     * @return \Maith\Common\AdminBundle\Entity\mAlbum 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set sfPath
     *
     * @param string $sfPath
     * @return mFile
     */
    public function setSfPath($sfPath)
    {
        $this->sfPath = $sfPath;
    
        return $this;
    }

    /**
     * Get sfPath
     *
     * @return string 
     */
    public function getSfPath()
    {
        return $this->sfPath;
    }
    
    public function getFullPath()
    {
      return $this->getPath().DIRECTORY_SEPARATOR.$this->getName();
    }

    /**
     * Set showName
     *
     * @param string $showName
     * @return mFile
     */
    public function setShowName($showName)
    {
        $this->showName = $showName;
    
        return $this;
    }

    /**
     * Get showName
     *
     * @return string 
     */
    public function getShowName()
    {
        return $this->showName;
    }
    
    /**
    * @ORM\PreRemove
    */
    public function simpleRemoveFiles()
    {
        //Chanchada para salir del paso!
        //$location = __DIR__.'/../../../../../app/cache';
        //$this->removeAllFiles($location);
    }
    
    public function removeAllFiles($location)
    {
      $finder = new Finder();
      $finder->in($location)->files()->name($this->getName());
      foreach($finder as $file)
      {
        @unlink($file->getRealpath());
      }
      @unlink($this->getFullPath());
    }
}
