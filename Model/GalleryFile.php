<?php

namespace Maith\Common\AdminBundle\Model;

use Symfony\Component\Finder\Finder;

/**
 * Description of GalleryFile
 *
 * @author rodrigo
 */
class GalleryFile {
  
  protected $filename;
  protected $fullpath;
  
  public function getFilename() {
    return $this->filename;
  }

  public function setFilename($filename) {
    $this->filename = $filename;
  }

  public function getFullpath() {
    return $this->fullpath;
  }

  public function setFullpath($fullpath) {
    $this->fullpath = $fullpath;
  }
  
  public static function removeAllCacheOfFile($cacheDir, $gallery, $filename)
  {
    $finder = new Finder();
    //$folder = $cacheDir."*".DIRECTORY_SEPARATOR.$gallery;
    $finder->in($cacheDir)->files()->name($filename);
    //$finder->in($folder)->files()->name($filename);
    foreach($finder as $file)
    {
      @unlink($file->getRealpath());
      //var_dump($file->getRealpath());
    }
  }


  
}


