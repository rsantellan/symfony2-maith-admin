<?php

namespace Maith\Common\AdminBundle\Model;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of BcuCotizadorData
 *
 * @author Rodrigo Santellan
 */
class BcuCotizadorData {
 
    public function retrieveTodayBcuCotization()
    {
        return retrieveLastUsableBcuCotizacion();
    }
    
    public function retrieveLastUsableBcuCotizacion($useCache = true, $d1 = null)
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


