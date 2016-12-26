<?php

namespace Maith\Common\AdminBundle\Services;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of MaithEmailService
 *
 * @author Rodrigo Santellan
 */
class MaithEmailService {
  
    protected $em;

    protected $logger;

    protected $container;

    protected $multipleMailers;

    protected $maximunPerHour;

    protected $mailersNames;

	public function __construct(ContainerInterface $container, EntityManager $em, Logger $logger, $multipleMailers = 0, $maximunPerHour = 99, $mailersNames = [])
	{
        $this->em = $em;
        $this->logger = $logger;
        $this->container = $container;
        $this->multipleMailers = $multipleMailers;
        $this->maximunPerHour = $maximunPerHour;
        $this->mailersNames = $mailersNames;
        $this->logger->addDebug('Starting Maith Email Service');
	}

	public function retriveMailersList()
	{
		if($this->multipleMailers == 0){
			return ['default'];
		}
		if(count($this->mailersNames) == 0){
			return array(
            'swiftmailer.mailer.first_mailer',
            'swiftmailer.mailer.second_mailer',
            'swiftmailer.mailer.third_mailer',
            'swiftmailer.mailer.fourth_mailer',
            'swiftmailer.mailer.fifth_mailer',
        	);
		}
		return $this->mailersNames;
	}

	public function sendWithAttachment($from, $to, $subject, $body, $attachments = [], $indexMailer = 0, $contenType = 'text/html')
	{
		$this->logger->addDebug('Sending new email');
		$mailer = $this->retrieveActiveMailer($indexMailer);
    	$message = \Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
            ->setSubject($subject)
            ->setContentType($contenType);
        foreach($attachments as $attachment){
        	if(file_exists ($attachment)){
        		$message->attach(\Swift_Attachment::fromPath($attachment));
        	}
        }
		if($mailer !== null){
			$this->logger->addDebug('Has mailer');
			try{
				$numSent = $mailer['mailer']->send($message);
	            $this->updateMailer($mailer['name'], $numSent);
	            return $numSent;
			}catch(\Exception $e){
				$this->logger->error($e);
				return 0;
			}	
            
		}
		$this->logger->error("No mailer is available");
		$this->logger->error($message);
		return 0;
	}

	public function send($from, $to, $subject, $body, $indexMailer = 0, $contenType = 'text/html')
	{
		return $this->sendWithAttachment($from, $to, $subject, $body, [], $indexMailer, $contenType);
	}

	public function retrieveActiveMailer($indexMailer = 0)
	{
		if($this->multipleMailers == 0){
			$this->logger->addDebug('Only one mailing interface');
			return $this->retrieveBasicMailer();
		}
		$testingMailersList = $this->retriveMailersList();
		$index = 0;
        $found = false;
        $mailer = null;
        $name = '';
        if($indexMailer > 0 && isset($testingMailersList[$indexMailer])){
        	try{
        		$mailer = $this->container->get($testingMailersList[$indexMailer]);
        		$name = $testingMailersList[$indexMailer];
                $found = $this->checkMailer($mailer, $name);
                
        	} catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $ex) {
        		$this->logger->error($ex);
        	} catch (\Exception $ex) {
            }	
        }
        if(!$found){
	        while($index < count($testingMailersList) && !$found){
	        	try{
	        		$mailer = $this->container->get($testingMailersList[$index]);
	        		$name = $testingMailersList[$index];
	                $found = $this->checkMailer($mailer, $name);
	                
	        	} catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $ex) {
	        		$this->logger->error($ex);
	        	} catch (\Exception $ex) {
	            }
	            $index++;
	        }	
        }
        if(!$found){
        	return $this->retrieveBasicMailer();
        }
        return ['mailer' => $mailer, 'name' => $name];
	}

	private function updateMailer($name, $quantity){
		$date = new \DateTime();
		$sql = 'update maith_mailer_cache set quantity = quantity + ? where name = ? and datestring = ?';
		$stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute(array($quantity, $name, $date->format('Y-m-d-H')));
	}

	private function checkMailer(\Swift_Mailer $mailer, $name)
	{
		$date = new \DateTime();
		$sql = 'select datestring, quantity, name from maith_mailer_cache where name = ? and datestring = ?';
		$stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute(array($name, $date->format('Y-m-d-H')));
        $row = $stmt->fetch();
        if($row){
	        if($row['quantity'] < $this->maximunPerHour){
	        	return true;	
	        }	
	        return false;
        }else{
        	$sql = 'insert into maith_mailer_cache (datestring, name, quantity) values (?, ?, 0)';
			$stmt = $this->em->getConnection()->prepare($sql);
	        $stmt->execute(array($date->format('Y-m-d-H'), $name));
        }
        return true;
	}

	private function retrieveBasicMailer()
	{
		$mailer = $this->container->get('mailer');
		if($this->checkMailer($mailer, 'mailer')){
			return ['mailer' => $mailer, 'name' => 'mailer'];
		};
		return null;
	}

}