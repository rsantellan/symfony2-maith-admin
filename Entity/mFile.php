<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Finder\Finder;
use Maith\Common\AdminBundle\Model\SimpleDiskCache;
use Maith\Common\AdminBundle\Model\OEmbededHandler;

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
     * 
     * Cache for using with online videos.
     * 
     **/   
    private $cachedata;
    
    
    private $imagesTypes = array(
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
    );
    
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
     * @var string
     *
     * @ORM\Column(name="onlinevideo", type="string", length=255, nullable=true)
     */
    private $onlinevideo = "";
    
    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;
        
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
    
    public function getCreated() {
      return $this->created;
    }

    public function setCreated($created) {
      $this->created = $created;
    }
    

    public function getOnlinevideo() 
    {
      return $this->onlinevideo;
    }

    public function setOnlinevideo($onlinevideo) 
    {
      $this->onlinevideo = $onlinevideo;
      return $this;
    }


    function __construct() 
    {
      $this->cachedata = new SimpleDiskCache(sys_get_temp_dir());
      
    }
    
    
    public function getThumb()
    {
      $wwwData = OEmbededHandler::retrieveData($this->getOnlinevideo());
      return $wwwData['data']['thumbnail_url'];
    }
    
    public function getOnlineVideoIframe()
    {
      $wwwData = OEmbededHandler::retrieveData($this->getOnlinevideo());
      return $wwwData['data']['html'];
    }
    
    public function getOnlineVideoPlayer()
    {
      switch ($this->getType()) {
        case 'youtube':
          $string = 'http://www.youtube.com/embed/%s?rel=0&amp;wmode=transparent';
          return sprintf($string, $this->getYouTubeVideoId($this->getOnlinevideo()));
          break;
        case 'vimeo':
          
          $wwwData = OEmbededHandler::retrieveData($this->getOnlinevideo());
          return 'http://player.vimeo.com/video/'.$wwwData['data']['video_id'];
          break;

        default:
        break;
      }
      
      // if image.type == 'youtube' or  image.type == 'vimeo'
      // http://player.vimeo.com/video/
      
      // http://www.youtube.com/embed/VOJyrQa_WR4?rel=0&amp;wmode=transparent
      
      $wwwData = OEmbededHandler::retrieveData($this->getOnlinevideo());
      return $wwwData['data']['html'];
    }
    
    private function getYouTubeVideoId($url)
    {
        $video_id = false;
        $url = parse_url($url);
        if (strcasecmp($url['host'], 'youtu.be') === 0)
        {
            #### (dontcare)://youtu.be/<video id>
            $video_id = substr($url['path'], 1);
        }
        elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
        {
            if (isset($url['query']))
            {
                parse_str($url['query'], $url['query']);
                if (isset($url['query']['v']))
                {
                    #### (dontcare)://www.youtube.com/(dontcare)?v=<video id>
                    $video_id = $url['query']['v'];
                }
            }
            if ($video_id == false)
            {
                $url['path'] = explode('/', substr($url['path'], 1));
                if (in_array($url['path'][0], array('e', 'embed', 'v')))
                {
                    #### (dontcare)://www.youtube.com/(whitelist)/<video id>
                    $video_id = $url['path'][1];
                }
            }
        }
        return $video_id;
    }
    
    public function isOnlineVideo()
    {
        if($this->getType() == 'youtube' || $this->getType() == 'vimeo')
        {
            return true;
        }
        return false;
    }
    
    public function isImage()
    {
        if(in_array($this->getType(), $this->imagesTypes))
        {
            return true;
        }
        return false;
    }
    
    public function getFileExtension()
    {
        return strtolower(substr(strrchr($this->getName(), "."), 1));
    }
    
    public function getKnownFileExtension()
    {
        $return = '';
        switch($this->getFileExtension())
        {
            case 'xls':
            case 'xlsx':
            case 'ods':
                $return = 'excel';
            break;
            
            case 'doc':
            case 'docx':
            case 'odt':
                $return = 'word';
            break;
            
            case 'ppt':
            case 'pptx':
            case 'ott':
                $return = 'powerpoint';
            break;
            
            case 'rtf':
            case 'txt':
            case 'pub':
                $return = 'text';
            break;
            
            case 'pdf':
                $return = 'pdf';
            break;
            
            default:
                $return = 'other';
            break;
        }
        return $return;
    }
}
