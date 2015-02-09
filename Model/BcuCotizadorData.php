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
  
  private function retrieveBcuCotization($urlToGo, $useCache = true)
  {
        $cacheData = $this->cachedata->get($urlToGo, $this->duration);
        if($cacheData && $useCache)
          return $cacheData;
        $headers = get_headers($urlToGo);
        $response = substr($headers[0], 9, 3);
        if ($response == "404") {
            throw new \Exception('url not found: '.$urlToGo);
        }
        
        $webdata = file_get_contents($urlToGo);
        $counter = 0;
        $finalData = array(
                'arbitrajes' => array(),
                'cotizaciones' => array(
                    'monedas' => array(),
                    'ui' => array()
                    ),
                );
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $webdata) as $line){
            // do stuff with $line
            
            if($counter > 4 && $counter < 35)
            {
                $objData = new \stdClass();
                $objData->country = trim(substr($line, 0, 16));
                $objData->currency = trim(substr($line, 17, 16));
                $objData->code = trim(substr($line, 40, 4));
                $objData->exchange = str_replace(' ', '  ',trim(substr($line, 45)));
                $finalData['arbitrajes'][$objData->code] = $objData; 
            }
            if(($counter > 37 && $counter < 44) || $counter == 47 )
            {
                $objData = new \stdClass();
                $objData->country = trim(substr($line, 0, 16));
                $objData->currency = trim(substr($line, 17, 16));
                $objData->code = trim(substr($line, 40, 4));
                $aux = str_replace(' ', '  ',trim(substr($line, 50)));
                $exploded = explode(' ', $aux);
                $objData->buy = $exploded[0];
                $objData->sell = '';
                $i = 1;
                while($i < count($exploded))
                {
                    if($exploded[$i] != '')
                    {
                        $objData->sell = $exploded[$i];
                    }
                    $i++;
                }
                $finalData['cotizaciones']['monedas'][$objData->code] = $objData;
            }
            if($counter > 43 && $counter < 47)
            {
                if($counter == 44)
                {
                    $ui = array();
                    $ui['values'] = array();
                }
                else
                {
                    $ui = $finalData['cotizaciones']['ui'];
                }
                $ui['country'] = trim(substr($line, 0, 16));
                $ui['currency'] = trim(substr($line, 17, 16));
                $ui['code'] = trim(substr($line, 40, 4));
                $aux = explode(' ', trim(substr($line, 54, 61)));
                $ui['values'][$aux[3]] = $aux[0];
                $finalData['cotizaciones']['ui'] = $ui;
            }
            $counter ++;
        }
        //var_dump($finalData['cotizaciones']['ui']);
        $this->cachedata->set($urlToGo, $finalData);
        return $finalData;     
    }

    public function retrieveTodayBcuCotization()
    {
        
        return $this->retrieveBcuCotization(sprintf('http://bcu.gub.uy/Cotizaciones/oicot%s.txt', date('dmy')));
    }
    
    public function retrieveLastUsableBcuCotizacion($useCache = true)
    {
        $lastdate = $this->cachedata->get('lastusedurl', 86400);
        if($lastdate && $useCache)
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
                $data = $this->retrieveBcuCotization(sprintf('http://bcu.gub.uy/Cotizaciones/oicot%s.txt', $d1->format('dmy')), $useCache);
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


