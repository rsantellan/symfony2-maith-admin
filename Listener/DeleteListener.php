<?php

namespace Maith\Common\AdminBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Description of DeleteListener
 *
 * @author Rodrigo Santellan
 */
class DeleteListener {
   
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        

        $query = $entityManager->createQuery("select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :id and a.object_class = :object_class")->setParameters(array('id' => $entity->getId(), 'object_class' => get_class($entity)));
        $albums = $query->getResult();
        foreach($albums as $album)
        {
            $entityManager->remove($album);
        }
    }
}


