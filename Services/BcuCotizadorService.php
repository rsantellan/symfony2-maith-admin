<?php

namespace Maith\Common\AdminBundle\Services;

use Doctrine\ORM\EntityManager;
use Maith\Common\AdminBundle\Model\BcuCotizadorData;
use Maith\Common\AdminBundle\Entity\mBcuDataType;
use Maith\Common\AdminBundle\Entity\mBcuCotizacion;
use Maith\Common\AdminBundle\Entity\mBcuUI;
use Maith\Common\AdminBundle\Entity\mBcuUpdated;

/**
 * Description of BcuCotizadorService
 *
 * @author Rodrigo Santellan
 */
class BcuCotizadorService {
  
  protected $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }
  
  
  public function retrieveLastUsableCotizations()
  {
    $d1 = new \DateTime();
    //Searching if today a query has been made.
    $dql = 'Select mu from MaithCommonAdminBundle:mBcuUpdated mu where mu.lastupdated = :datetime';
    $updated = $this->em->createQuery($dql)
                ->setMaxResults(1)
                ->setParameters(array(
                    'datetime' => $d1->format('Y-m-d')
                ))  
                ->getOneOrNullResult();
    
    if($updated == null){
      $this->generateData($d1);
    }
    $finalData = array(
          'arbitrajes' => array(),
          'cotizaciones' => array(
              'monedas' => array(),
              'ui' => array()
              ),
          );
    $finalData = $this->retrieveLastCotizaciones($finalData);
    
    $finalData = $this->retrieveUiOfLastMonth($finalData);
  }
  
  public function retrieveLastCotizaciones($finalData = null)
  {
    if($finalData == null)
    {
      $finalData = array(
          'arbitrajes' => array(),
          'cotizaciones' => array(
              'monedas' => array(),
              'ui' => array()
              ),
          );
    }
    $idsSql = 'select id from maith_bcu_data where valueDate = (select max(valueDate) from maith_bcu_data)';
    $conn = $this->em->getConnection();
    $stmt = $conn->prepare($idsSql);
    $stmt->execute();
    $list = $stmt->fetchAll();
    $idList = array();
    foreach($list as $id)
    {
      $idList[] = $id['id'];
      
    }
    $dql = 'Select mc, mcd from MaithCommonAdminBundle:mBcuCotizacion mc join mc.type mcd where mc.id in (:idList) and mcd.visible = true';
    $objects = $this->em->createQuery($dql)
                  ->setParameter('idList', $idList)
                  ->getResult();
    
    foreach($objects as $object)
    {
      $objData = new \stdClass();
      $objData->country = $object->getType()->getCountry();
      $objData->currency = $object->getType()->getCurrency();
      $objData->code = $object->getType()->getCode();
      $objData->buy = $object->getBuy();
      $objData->sell = $object->getSell();
      $finalData['cotizaciones']['monedas'][$objData->code] = $objData;
    }
    return $finalData;
  }
  
  public function retrieveUiOfLastMonth($finalData = null)
  {
    $date = new \DateTime();
    $date->modify("last day of previous month");
    $dql = 'Select mu from MaithCommonAdminBundle:mBcuUpdated mu where mu.lastupdated = :datetime';
    $updated = $this->em->createQuery($dql)
                ->setMaxResults(1)
                ->setParameters(array(
                    'datetime' => $date->format('Y-m-d')
                ))  
                ->getOneOrNullResult();
    
    if($updated == null){
      $this->generateData($date);
    }
    if($finalData == null)
    {
      $finalData = array(
          'arbitrajes' => array(),
          'cotizaciones' => array(
              'monedas' => array(),
              'ui' => array()
              ),
          );
    }
    
    
    
  }
  
  private function generateData($datetime = null)
  {
    if($datetime == null)
    {
      $datetime = new \DateTime();
    }
    $generateDateTime = clone $datetime;
    $cotizador = new BcuCotizadorData();
    $data = $cotizador->retrieveLastUsableBcuCotizacion(false, $datetime);
    $cotizaciones = $data['cotizaciones'];
    $uiData = $cotizaciones['ui']['values'];
    foreach($cotizaciones["monedas"] as $cotizacion)
    {
      
      $type = $this->em->getRepository('MaithCommonAdminBundle:mBcuDataType')->findOneBy(array('code' => $cotizacion->code));
      if($type == null){
        $type = new mBcuDataType();
        $type->setCountry($cotizacion->country);
        $type->setCurrency($cotizacion->currency);
        $type->setCode($cotizacion->code);
        $type->setVisible(true);
        $this->em->persist($type);
        $this->em->flush();
      }
      $dbData = new mBcuCotizacion();
      $dbData->setBuy($cotizacion->buy);
      $dbData->setSell($cotizacion->sell);
      $dbData->setType($type);
      $dbData->setValueDate($datetime);
      $this->em->persist($dbData);
      $this->em->flush();
      
    }
    foreach($uiData as $uiDate => $uiValue)
    {
      $uiDatetime = \DateTime::createFromFormat('j/m/y', trim($uiDate));
      $uiDb = new mBcuUI();
      $uiDb->setValue($uiValue);
      $uiDb->setValueDate($uiDatetime);
      $this->em->persist($uiDb);
      $this->em->flush();
    }
    $mBcuUpdated = new mBcuUpdated();
    $mBcuUpdated->setLastupdated($generateDateTime);
    $this->em->persist($mBcuUpdated);
    $this->em->flush();
  }
  
}
