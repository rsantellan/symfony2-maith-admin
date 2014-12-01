<?php

namespace Maith\Common\AdminBundle\Model;


/**
 * Description of BcuCotizadorData
 *
 * @author rodrigo
 */
class BcuCotizadorData {
  
  private $cachedata;
  private $duration = 604800;
  
  public function __construct()
  {
    $this->cachedata = new SimpleDiskCache(sys_get_temp_dir());
    // 604800
  }
  
  private function retrieveBcuCotization($urlToGo)
  {
        $cacheData = $this->cachedata->get($urlToGo, $this->duration);
        if($cacheData)
          return $cacheData;
        $headers = get_headers($urlToGo);
        $response = substr($headers[0], 9, 3);
        if ($response == "404") {
            throw new \Exception('url not found: '.$urlToGo);
        }

        $webdata = file_get_contents($urlToGo);
        $counter = 0;
        $finalData = array();
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $webdata) as $line){
            // do stuff with $line
            
            if($counter > 4 && $counter < 35)
            {
                $objData = new \stdClass();
                $objData->country = trim(substr($line, 0, 16));
                $objData->currency = trim(substr($line, 17, 16));
                $objData->code = trim(substr($line, 40, 4));
                $objData->exchange = str_replace(' ', '  ',trim(substr($line, 45)));
                $finalData[$objData->code] = $objData; 
            }
            $counter ++;
        }
        $this->cachedata->set($urlToGo, $finalData);
        return $finalData;     
    }

    public function retrieveTodayBcuCotization()
    {
        
        return $this->retrieveBcuCotization(sprintf('http://bcu.gub.uy/Cotizaciones/oicot%s.txt', date('dmy')));
    }
    
    public function retrieveLastUsableBcuCotizacion()
    {
        $lastdate = $this->cachedata->get('lastusedurl', 86400);
        if($lastdate)
        {
          return $this->retrieveBcuCotization(sprintf('http://bcu.gub.uy/Cotizaciones/oicot%s.txt', $lastdate));
        }
        $d1 = new \DateTime();
        $data = null;
        $found = false;
        while(!$found)
        {
            try
            {
                $data = $this->retrieveBcuCotization(sprintf('http://bcu.gub.uy/Cotizaciones/oicot%s.txt', $d1->format('dmy')));
                $found = true;
                $this->cachedata->set('lastusedurl', $d1->format('dmy'));
            }
            catch(\Exception $e)
            {
                $d1->modify('-1 day');
            }
        }
        return $data;
    }
}


