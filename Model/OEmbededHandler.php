<?php

namespace Maith\Common\AdminBundle\Model;

use Maith\Common\AdminBundle\Model\SimpleDiskCache;

/**
 * Description of OEmbededHandler
 *
 * @author rodrigo
 */
class OEmbededHandler {
  
  public static function retrieveData($videoUrl)
  {
    $cachedata = new SimpleDiskCache(sys_get_temp_dir());
    $cacheContent = $cachedata->get($videoUrl, 1);
    if($cacheContent){
      //return $cacheContent;
    }
    $videoType = NULL;
    //$message = 'El video no existe';
    //$name = NULL;
    $data = array();
    if (strpos($videoUrl, 'youtube') > 0) {
        $videoType = 'youtube';
    } elseif (strpos($videoUrl, 'vimeo') > 0) {
        $videoType = 'vimeo';
    } 
    if ($videoType != NULL) 
    {
      //$videoExists = false;
      if($videoType == 'youtube')
      {
        $json = @file_get_contents('http://www.youtube.com/oembed?url='.urlencode($videoUrl).'&format=json');
        if($json)
        {
          $data = json_decode($json, true);
          //$videoExists = true;
          //$name = $decode["title"];
        }
      }
      else
      {
        if($videoType == 'vimeo')
        {
          $json = @file_get_contents('https://vimeo.com/api/oembed.json?url='.urlencode($videoUrl));
          if($json)
          {
            $data = json_decode($json, true);
            //$videoExists = true;
            //$name = $decode["title"];
          }
        }
      }
    }
    $return = array(
        'videoType' => $videoType,
        'data' => $data,
    );
    if(count($data) > 0)
    {
      $cachedata->set($videoUrl, $data);
    }
    return $return;
    
  }
}


