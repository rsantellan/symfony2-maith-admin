<?php

namespace Maith\Common\AdminBundle\Twig;

use Doctrine\ORM\EntityManager;

/**
 * Description of mImageExtension
 *
 * @author rodrigo
 */
class mAvatarExtension extends \Twig_Extension
{
  
  private $em;
  private $conn;
  private $rootDir;
  
  function __construct(EntityManager $em, $rootDir) {
    $this->em = $em;
    $this->conn = $em->getConnection();
    $this->rootDir = $rootDir;
  }

  
  public function getFilters() {
    return array(
        new \Twig_SimpleFilter('mAvatar', array($this, 'mAvatarFilter'))
    );
  }
  
  public function mAvatarFilter($objectId, $objectClass, $albumName = "Default", $position = 1, $cache = False)
  {
    $query = $this->em->createQuery("select f from MaithCommonAdminBundle:mFile f join f.album a where a.object_id = :id and a.object_class = :object_class and a.name = :name order by f.orden ASC");
    //$query = $this->em->createQuery("select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :id and a.object_class = :object_class and a.name = :name ");
    $query->setParameters(array('id' => $objectId, 'object_class' => $objectClass, 'name' => $albumName));
    $position --;
    $query->setFirstResult($position);
    $query->setMaxResults(1);
    if($cache)
    {
        $query->useResultCache(true, 360);
    }
    $file = $query->getOneOrNullResult();
    if($file !== null)
    {
        return $file->getFullPath();
    }
    $firstPath = $this->rootDir.'/../web/images/noimage.png';
    if(is_file($firstPath))
    {
      return $firstPath;
    }
    return $this->rootDir."/../web/bundles/maithcommonimage/images/noimage.png";
  }

  public function getName() {
    return "maith_m_avatar";
  }
}

