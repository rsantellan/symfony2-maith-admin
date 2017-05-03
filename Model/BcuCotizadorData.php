<?php

namespace Maith\Common\AdminBundle\Model;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of BcuCotizadorData
 *
 * @author Rodrigo Santellan
 */
class BcuCotizadorData {
 
    private $cachedata;
    private $duration = 604800;

    public function __construct()
    {
        $this->cachedata = new SimpleDiskCache(sys_get_temp_dir());
        // 604800
    }

    public function retrieveTodayBcuCotization()
    {
        return $this->retrieveLastUsableBcuCotizacion();
    }
    
    public function retrieveLastUsableBcuCotizacion($useCache = true, $d1 = null)
    {
        if($d1 == null){
            $d1 = new \DateTime();
        }
        $cacheData = $this->cachedata->get('bcu_cotizaciones_'.$d1->format('dmY'), $this->duration);
        if($cacheData && $useCache){
          return $cacheData;
        }
        $finalData = array(
                'arbitrajes' => array(),
                'cotizaciones' => array(
                    'monedas' => array(),
                    'ui' => array()
                    ),
                );
        $finalData['cotizaciones']['monedas'] = $this->doRetrieveData($d1, true);
        $finalData['cotizaciones']['ui'] = $this->doRetrieveData($d1, false);
        $cacheData = $this->cachedata->set('bcu_cotizaciones_'.$d1->format('dmY'), $finalData);
        
    }

    private function doRetrieveData($d1 = null, $monedas = true)
    {
        $codigosAceptados = array("USD", "EURO", "CHF", "GBP", "ARS", "BRL", "JPY", "U.I.");
        $last_date_found = false;
        $returnList = [];
        $counter = 0;
        while (!$last_date_found && $counter < 60) {
            $fecha = $d1->format('d/m/Y');//date("d") - $diff . "/" . date("m") . "/" . date("Y");
            if ($monedas) {
                $data = '{"KeyValuePairs":{"Monedas":[{"Val":"500","Text":"PESO ARGENTINO"},{"Val":"1000","Text":"REAL"},{"Val":"1111","Text":"EURO"},{"Val":"2222","Text":"DOLAR USA"},{"Val":"2700","Text":"LIBRA ESTERLINA"},{"Val":"3600","Text":"YEN"},{"Val":"5900","Text":"FRANCO SUIZO"}],"FechaDesde":"' . $fecha . '","FechaHasta":"' . $fecha . '","Grupo":"1"}}';
            } else {
                $data = '{"KeyValuePairs":{"Monedas":[{"Val":"9800","Text":"UNIDAD INDEXADA"}],"FechaDesde":"' . $fecha . '","FechaHasta":"' . $fecha . '","Grupo":"2"}}';
            }
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/json",
                    'method' => 'POST',
                    'content' => $data
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result !== FALSE) {
                $result = json_decode($result);
                if ($result->cotizacionesoutlist->RespuestaStatus->status === 0) {
                    $d1->modify('-1 day');
                } else {
                    $last_date_found = true;
                    foreach ($result->cotizacionesoutlist->Cotizaciones as $cotizacion) {
                        if (in_array($cotizacion->CodigoISO, $codigosAceptados)) {
                            $stdCotizacion = new \stdClass();
                            $stdCotizacion->country = $cotizacion->CodigoISO;
                            $stdCotizacion->name = $cotizacion->Nombre;
                            $stdCotizacion->value = $cotizacion->TCV;
                            $stdCotizacion->code = $cotizacion->Moneda;
                            $stdCotizacion->date = $this->fixDotNetJsonDate($cotizacion->Fecha);
                            $stdCotizacion->arbitraje = $cotizacion->ArbAct;
                            $returnList[] = $stdCotizacion;
                        }
                    }
                }
            }
            $counter++;
        }
        return $returnList;
    }
    
    private function fixDotNetJsonDate($fecha)
    {
        $fecha = str_replace('/Date(', '', $fecha);
        $fecha = str_replace(')/', '', $fecha);
        $fecha = substr($fecha, 0, 10);
        $datetime = new DateTime();
        $newDate = $datetime->createFromFormat('m-d-Y', date( 'm-d-Y', $fecha));
        return $newDate;
    }

    public function retrieveLastUsableBcuCotizacionHtml($useCache = true, $d1 = null)
    {
        $url = 'http://www.bcu.gub.uy/Estadisticas-e-Indicadores/Paginas/Cotizaciones.aspx';
        // Testing
        $url = '/home/rodrigo/proyectos/s2/contable3/symfony/Cotizaciones.aspx';
        $crawler = new Crawler(file_get_contents($url));
        $tr_elements = $crawler->filterXPath("//*[@id='bcuCotizacionContent']/div[2]/div[3]/ul/li[2]/table/tr");
        $rows = array();
        foreach ($tr_elements as $i => $content) {
            $tds = array();
            // create crawler instance for result
            $crawler = new Crawler($content);
            //iterate again
            foreach ($crawler->filter('td') as $i => $node) {
               // extract the value
                $tds[] = $node->nodeValue;
            }
            $rows[] = $tds;
        }
        return $rows;
    }
}


