<?php

namespace Maith\Common\AdminBundle\Services;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;
/**
 * Description of MaithParametersService
 *
 * @author Rodrigo Santellan
 */
class MaithParametersService
{

    private $kernelRootDirectory;
    private $filePath = null;

    public function __construct($kernelRootDirectory)
    {
      $this->kernelRootDirectory = $kernelRootDirectory;
    }

    public function generateFilePath($filePath = null)
    {
      if($filePath === null)
      {
         $this->filePath = $this->kernelRootDirectory.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'my-parameters.yml';
      }
      else
      {
        $this->filePath = $filePath;
      }
    }

    public function getFilePath()
    {
      if($this->filePath === null)
      {
        $this->generateFilePath();
      }
      return $this->filePath;
    }

    public function getParametersList()
    {
      try{
        return Yaml::parse(file_get_contents($this->getFilePath()));

      }catch(\Exception $e)
      {
        return array();
      }
    }

    public function saveParameters($saveDataArray)
    {
      $dumper = new Dumper();
      $yaml = $dumper->dump(array('parameters' => $saveDataArray), 2);
      file_put_contents($this->getFilePath(), $yaml);
    }

    public function getParameter($paramName)
    {
      $list = $this->getParametersList();
      if(isset($list['parameters'][$paramName]))
      {
          return $list['parameters'][$paramName];
      }
      return NULL;
    }
}
