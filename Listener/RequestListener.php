<?php

namespace Maith\Common\AdminBundle\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Maith\Common\AdminBundle\Model\Encrypt;

/**
 * Description of RequestListener
 *
 * @author rodrigo
 */
class RequestListener {

  protected $token;

  public function __construct($token) {
    $this->token = $token;
  }

  public function onKernelRequest(GetResponseEvent $event) {
    
    
    if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
      return;
    }
    
    $request = $event->getRequest();
    
    //$logger = $GLOBALS['kernel']->getContainer()->get('logger');
    //$logger->info("Estoy en 'RequestListener' service ");
    //$logger->info("Estoy en 'RequestListener' service ".$request);
    //$logger->info("Estoy en 'RequestListener' service attributes ".implode(" | ", $request->attributes->keys()));
    //$logger->info("Estoy en 'RequestListener' service request ".implode(" | ", $request->request->keys()));
    /*
    $logger->info("Estoy en 'RequestListener' service request 1 ". $request->request->get('data-session'));
    $logger->info("Estoy en 'RequestListener' service query ".implode(" | ", $request->query->keys()));
    $logger->info("Estoy en 'RequestListener' service server ".implode(" | ", $request->server->keys()));
    $logger->info("Estoy en 'RequestListener' service header ".implode(" | ", $request->headers->keys()));
    $logger->info("Estoy en 'RequestListener' service content type".$request->getContentType());
    $logger->info("Estoy en 'RequestListener' service content ".$request->getContent());
    $logger->info("Estoy en 'RequestListener' service request format".$request->getRequestFormat());
    */
    //$logger->info("Estoy en 'RequestListener' service: ---->  ".$request->request->get('data-session'));
    //$logger->info("Estoy en 'RequestListener' service: ---->  ".$request->request->get('_myUploader'));
    //$logger->info("Estoy en 'RequestListener' service: ---->  ".$request->getQueryString());
    if ($request->request->get('data-session') && $request->request->get('_myUploader')) {
      //$logger->info("Estoy en 'RequestListener' service (Injecting session): ---->  ".$request->request->get('data-session'));
      $request->cookies->set(session_name(), 1);
      session_id($this->decrypt($request->request->get('data-session')));
    }
  }

  protected function decrypt($string) {
    $crypt = new Encrypt($this->token);
    return $crypt->decrypt(preg_replace('/ /', '+', $string));
  }

}

