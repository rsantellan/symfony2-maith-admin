<?php

namespace Maith\Common\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Maith\ContableBundle\Entity\ContactEmail;


class BcuUpdateDataCommand extends ContainerAwareCommand
{
	protected function configure()
    {
        $this
            ->setName('maith:bcuUpdate')
            ->setDescription('Updating bcu old data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bcuCotizador = $this->getContainer()->get('maith_common.bcucotizador');
        $bcuCotizador->retrieveLastUsableCotizations();
        

    }
}