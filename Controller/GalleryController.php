<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;

class GalleryController extends Controller
{
    public function indexAction()
    {
        $targetDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.'galleries';
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
        return $this->render('MaithCommonAdminBundle:Gallery:index.html.twig', array(
                'galleries' => $directories,
            ));    
        
    }

}
