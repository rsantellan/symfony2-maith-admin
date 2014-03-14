<?php

namespace Maith\Common\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ProjectRepository
 *
 * @author Rodrigo Santellan
 */
class mAlbumRepository extends EntityRepository{
  
  public function retrieveByObjectIdAndClass($object_id, $object_class)
  {
    $dql = "select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :object_id and a.object_class = :object_class";
    return $this->getEntityManager()->createQuery($dql)->setParameters(array('object_id' => $object_id, 'object_class' => $object_class))->getResult();
  }
  
  public function retrieveByObjectIdClassAndAlbumName($object_id, $object_class, $name, $onlyone = true)
  {
    
  }
  
}

