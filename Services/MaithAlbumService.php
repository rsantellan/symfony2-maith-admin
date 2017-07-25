<?php

namespace Maith\Common\AdminBundle\Services;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of MaithAlbumService
 *
 * @author Rodrigo Santellan
 */
class MaithAlbumService {

    protected $em;

    protected $logger;

	public function __construct(EntityManager $em, Logger $logger)
	{
        $this->em = $em;
        $this->logger = $logger;
        $this->logger->addDebug('Starting Maith Album Service');
	}

	public function retrieveAllFilesByObject($objectclass, $objectId, $useName = true)
	{
		$dql = 'select f, a from MaithCommonAdminBundle:mFile f join f.album a where a.object_id = :object_id and a.object_class = :object_class order by a.name desc, f.orden ASC';
		$dbdata = $this->em
					->createQuery($dql)
					->setParameters(['object_id' => $objectId, 'object_class' => $objectclass, ])
					->getResult();
		$data = [];
		foreach($dbdata as $mFile){
			$key = $mFile->getAlbum()->getId();
			if($useName){
				$key = $mFile->getAlbum()->getName();
			}
			if(!isset($data[$key])){
				$data[$key] = [];
			}
			$data[$key][] = $mFile;
		}
		return $data;
	}

	public function retrieveAllFilesByObjectAndAlbum($objectclass, $objectId, $album = 'main')
	{
		$dql = 'select f, a from MaithCommonAdminBundle:mFile f join f.album a where a.name = :name and a.object_id = :object_id and a.object_class = :object_class order by a.name desc, f.orden ASC';
		return $this->em
					->createQuery($dql)
					->setParameters(['object_id' => $objectId, 'object_class' => $objectclass, 'name' => $album])
					->getResult();
	}	
}