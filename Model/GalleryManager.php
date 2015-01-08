<?php

namespace Maith\Common\AdminBundle\Model;

use Symfony\Component\Finder\Finder;

/**
 * Description of GalleryManager
 *
 * @author Rodrigo Santellan
 */
class GalleryManager {
  
    private $documentRoot;
  
    private $galleryRoot = 'galleries';
    
    public function __construct($documentRoot) {
      $this->documentRoot = $documentRoot;
    }
    
    public function getDocumentRoot() {
      return $this->documentRoot;
    }

    public function setDocumentRoot($documentRoot) {
      $this->documentRoot = $documentRoot;
    }
    
    public function getGalleryRoot() {
      return $this->galleryRoot;
    }

    public function setGalleryRoot($galleryRoot) {
      $this->galleryRoot = $galleryRoot;
    }

    public function getGalleriesFullPath()
    {
      return $this->getDocumentRoot().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$this->getGalleryRoot();
    }
    
    public function getAllGalleriesWithData()
    {
      $targetDir = $this->getGalleriesFullPath();
      if(!is_dir($targetDir))
      {
        mkdir($targetDir);
      }
      $finder = new Finder();
      $finder->in($targetDir)->directories()->sortByName();
      $directories = array();
      foreach($finder->getIterator() as $directory)
      {
        $directories[$directory->getRelativePathname()] = array();
        $fileFinder = new Finder();
        $fileFinder->in($directory->getPathname())->files()->sortByChangedTime();
        foreach($fileFinder->getIterator() as $file)
        {
          $directories[$directory->getRelativePathname()][] = $file;
        }
      }
      return $directories;
    }
    
    public function getGalleryFiles($gallery)
    {
      $targetDir = $this->getGalleriesFullPath().DIRECTORY_SEPARATOR.$gallery;
      $files = array();
      if(!is_dir($targetDir))
      {
        return $files;
      }
      $fileFinder = new Finder();
      $fileFinder->in($targetDir)->files()->sortByChangedTime();
      
      foreach($fileFinder->getIterator() as $file)
      {
        $files[] = $file;
      }
      return $files;
    }
    
    public function getGalleryFile($gallery, $filename)
    {
      $targetDir = $this->getGalleriesFullPath().DIRECTORY_SEPARATOR.$gallery;
      if(!is_dir($targetDir))
      {
        return null;
      }
      $files = array();
      $fileFinder = new Finder();
      $fileFinder->in($targetDir)->files()->name($filename);
      foreach($fileFinder->getIterator() as $file)
      {
        $files[] = $file;
      }
      return array_pop($files);
    }

    public function removeGalleryFile($gallery, $filename)
    {
      $targetDir = $this->getGalleriesFullPath().DIRECTORY_SEPARATOR.$gallery;
      if(!is_dir($targetDir))
      {
        return null;
      }
      $files = array();
      $fileFinder = new Finder();
      $fileFinder->in($targetDir)->files()->name($filename);
      foreach($fileFinder->getIterator() as $file)
      {
        $files[] = $file;
      }
      $spFile = array_pop($files);
      return @unlink($spFile->getPathName());
    }
}


