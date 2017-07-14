<?php

namespace Maith\Common\AdminBundle\Services;

use Monolog\Logger;
use Symfony\Component\Finder\Finder;

/**
 * Description of BcuCotizadorService
 *
 * @author Rodrigo Santellan
 */
class StaticFilesService 
{
	protected $logger;

	private $documentRoot;
  
    private $filesRoot = 'galleries';

	public function __construct(Logger $logger, $documentRoot, $filesRoot)
	{
		$this->logger = $logger;
		$this->documentRoot = $documentRoot;
		$this->filesRoot = $filesRoot;
		$this->logger->addDebug('Starting Static files Service');
	}

	public function getDocumentRoot() {
      return $this->documentRoot;
    }

    public function setDocumentRoot($documentRoot) {
      $this->documentRoot = $documentRoot;
    }
    
    public function getFilesRoot() {
      return $this->filesRoot;
    }

    public function setFilesRoot($filesRoot) {
      $this->filesRoot = $filesRoot;
    }

    public function getFilesFullPath()
    {
      return $this->getDocumentRoot().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$this->getFilesRoot();
    }
    
    public function getAllFilesWithData()
    {
      $targetDir = $this->getFilesFullPath();
      $this->logger->addDebug("The files full path is: ", [$targetDir]);
      if(!is_dir($targetDir))
      {
        mkdir($targetDir);
      }
      $finder = new Finder();
      $finder->in($targetDir)->directories()->sortByName();
      $directories = array();
      foreach($finder->getIterator() as $directory)
      {
      	$this->logger->addDebug("directory is: ",[$directory]);
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
    
    public function createFolder($folderName)
    {
    	$targetDir = $this->getFilesFullPath().DIRECTORY_SEPARATOR.$folderName;
    	$this->logger->addDebug("The new folder path is: ", [$targetDir]);
    	$response = true;
    	if(!is_dir($targetDir))
	    {
	        $response = mkdir($targetDir);
	    }
	    return $response;
    }

    public function checkFolder($folderName){
    	if($this->createFolder($folderName)){
    		return $this->getFilesFullPath().DIRECTORY_SEPARATOR.$folderName;
    	}
    	return NULL;
    }

    public function getFiles($folder)
    {
      $targetDir = $this->getFilesFullPath().DIRECTORY_SEPARATOR.$folder;
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
    
    public function getFile($folder, $filename)
    {
      $targetDir = $this->getFilesFullPath().DIRECTORY_SEPARATOR.$folder;
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

    public function removeFile($folder, $filename)
    {
      $targetDir = $this->getFilesFullPath().DIRECTORY_SEPARATOR.$folder;
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