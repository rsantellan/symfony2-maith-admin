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
              'ui' => array('values' => array())
              ),
          );
    $finalData = $this->retrieveLastCotizaciones($finalData);
    
    return $this->retrieveUiOfLastMonth($finalData);
    
    
  }
  
  public function retrieveLastCotizaciones($finalData = null)
  {
    if($finalData == null)
    {
      $finalData = array(
          'arbitrajes' => array(),
          'cotizaciones' => array(
              'monedas' => array(),
              'ui' => array('values' => array())
              ),
          );
    }
    $idsSql = 'select id from maith_bcudata where valueDate = (select max(valueDate) from maith_bcudata)';
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
      $objData->currency = $object->getType()->getName();
      $objData->name = $object->getType()->getName();
      $objData->buy = $object->getBuy();
      $objData->sell = $object->getSell();
      $finalData['cotizaciones']['monedas'][$objData->name] = $objData;
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
              'ui' => array('values' => array())
              ),
          );
    }
    $sql = "select id, value, valueDate from maith_bcuui where valueDate <= DATE_FORMAT(NOW() ,'%Y-%m-01') order by valueDate desc limit 5";
    $conn = $this->em->getConnection();
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $list = $stmt->fetchAll();
    foreach($list as $uiData)
    {
      $finalData['cotizaciones']['ui']['values'][$uiData["valueDate"]] = $uiData["value"];
    }
    return $finalData;
    
  }
  
  public function retrieveUiOfDate(\DateTime $date, $quantity = 0)
  {
      $dql = 'select ui from MaithCommonAdminBundle:mBcuUI ui where ui.valueDate ';
      if($quantity >= 3){
          $dql .= '<= :date';
      }else{
          $dql .= '= :date';
      }
      $dql .= ' order by ui.valueDate desc';
      $cotization = $this->em->createQuery($dql)
                    ->setParameters(array('date' => $date->format('Y-m-d')))
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
      if($cotization){
          return $cotization;
      }
      $clonedDateTime = clone $date;
      $days = 0;
      while($days < $quantity){
          $clonedDateTime->modify('-1 day');
          $days ++;
      }
      // Safe guard of the loop.
      if($quantity == 4){
          return null;
      }
      $this->generateData($clonedDateTime);
      return $this->retrieveUiOfDate($date, $quantity + 1);
  }

  public function retrieveCurrencyOfDate(\DateTime $date, $currency = 'USD', $quantity = 0)
  {
      $dql = 'Select mc, mcd from MaithCommonAdminBundle:mBcuCotizacion mc join mc.type mcd';
      $parameters = array('currency' =>  $currency, 'date' => $date->format('Y-m-d'));
      $conditions = array('mcd.currency = :currency');
      if($quantity >= 3){
        $conditions[] = 'mc.valueDate <= :date';
      }else{
        $conditions[] = 'mc.valueDate = :date';
      }
      $dql .= " where ".implode(" and ", $conditions). ' order by mc.valueDate desc';
      $cotization = $this->em->createQuery($dql)
                  ->setParameters($parameters)
                  ->setMaxResults(1)
                  ->getOneOrNullResult();
      if($cotization){
        return $cotization;
      }
      $clonedDateTime = clone $date;
      $days = 0;
      while($days < $quantity){
        $clonedDateTime->modify('-1 day');
        $days ++;
      }
      // Safe guard of the loop.
      if($quantity == 4){
        return null;
      }
      $this->generateData($clonedDateTime);
      return $this->retrieveCurrencyOfDate($date, $currency, $quantity + 1);
  }

  private function generateData($datetime = null)
  {
    if($datetime == null)
    {
      $datetime = new \DateTime();
    }
    $generateDateTime = clone $datetime;
    $cotizador = new BcuCotizadorData();
    $data = $cotizador->retrieveLastUsableBcuCotizacion(false, $generateDateTime);
    $cotizaciones = $data['cotizaciones'];
    foreach($cotizaciones["monedas"] as $cotizacion)
    {
      $type = $this->em->getRepository('MaithCommonAdminBundle:mBcuDataType')->findOneBy(array('code' => $cotizacion->code));

      if($type == null){
        $type = new mBcuDataType();
        $type->setCountry($cotizacion->country);
        $type->setCurrency($cotizacion->country);
        $type->setCode($cotizacion->code);
        $type->setName($cotizacion->name);
        $type->setVisible(true);
        $this->em->persist($type);
      }
      $dbData = $this->em->getRepository('MaithCommonAdminBundle:mBcuCotizacion')->findOneBy(array('valueDate' => $generateDateTime, 'type' => $type));  
      
      if($dbData == null){
        $dbData = new mBcuCotizacion();
        $dbData->setBuy($cotizacion->value);
        $dbData->setSell($cotizacion->value);
        $dbData->setType($type);
        $dbData->setValueDate($generateDateTime);
        $this->em->persist($dbData);
      }
      //$this->em->flush();
    }
    foreach($cotizaciones['ui'] as $ui)
    {
      $uiDb = $this->em->getRepository('MaithCommonAdminBundle:mBcuUI')->findOneBy(array('valueDate' => $ui->date));
      if($uiDb == null){
        $uiDb = new mBcuUI();
        $uiDb->setValue($ui->value);
        $uiDb->setValueDate($ui->date);
        $this->em->persist($uiDb);  
      }
    }
    $mBcuUpdated = new mBcuUpdated();
    $mBcuUpdated->setLastupdated($datetime);
    $this->em->persist($mBcuUpdated);
    $this->em->flush();
  }

  private function generateDataHtml($datetime = null)
  {
    if($datetime == null)
    {
      $datetime = new \DateTime();
    }
    $generateDateTime = clone $datetime;
    $cotizador = new BcuCotizadorData();
    $data = $cotizador->retrieveLastUsableBcuCotizacionHtml(false, $generateDateTime);
    $uiname = "UNIDAD INDEXADA";
    foreach($data as $currency){
      $name = $currency[0];
      $date = $currency[1];
      $buy = $currency[2];
      $sell = $currency[3];
      $arbitraje = $currency[4];
      $type = $this->em->getRepository('MaithCommonAdminBundle:mBcuDataType')->findOneBy(array('name' => $name));
      if($type == null){
        $type = new mBcuDataType();
        $type->setName($name);
        $type->setVisible(true);
        $this->em->persist($type);
        $this->em->flush();
      }
      $generatedDatetime = \DateTime::createFromFormat('j/m/Y', trim($date));
      if($name == $uiname){
        $uiDb = $this->em->getRepository('MaithCommonAdminBundle:mBcuUI')->findOneBy(array('valueDate' => $generatedDatetime));
        if($uiDb == null){
          $uiDb = new mBcuUI();
          $uiDb->setValue($sell);
          $uiDb->setValueDate($generatedDatetime);
          $this->em->persist($uiDb);
          $this->em->flush();
        }
      }else{
        $dbData = $this->em->getRepository('MaithCommonAdminBundle:mBcuCotizacion')->findOneBy(array('valueDate' => $generatedDatetime, 'type' => $type));
        if($dbData == null){
          try{
            $dbData = new mBcuCotizacion();
            $dbData->setBuy($buy);
            $dbData->setSell($sell);
            $dbData->setType($type);
            $dbData->setValueDate($generatedDatetime);
            $this->em->persist($dbData);
            $this->em->flush();
          }catch(\Exception $e){
            var_dump($e->getMessage());
          }    
        }
        
      }
    }
    $mBcuUpdated = new mBcuUpdated();
    $mBcuUpdated->setLastupdated($generateDateTime);
    $this->em->persist($mBcuUpdated);
    $this->em->flush();
  }
  
}
