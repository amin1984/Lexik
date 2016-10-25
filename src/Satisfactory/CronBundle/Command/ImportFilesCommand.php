<?php

namespace Satisfactory\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFilesCommand extends ContainerAwareCommand
{

    protected $_in_folder;
    protected $_out_folder;

    protected function configure()
    {
        $this->setName("cron:importfiles")
            ->setDescription("Import js file in mongo db");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->_in_folder = $this->getContainer()->get('kernel')->getRootDir() . '/../datas/in/';
        $this->_out_folder = $this->getContainer()->get('kernel')->getRootDir() . '/../datas/out/';

        $handle = opendir($this->_out_folder);
        while (($file = readdir($handle)) !== false) {
            if (!preg_match('/(.*)\.js/', $file))
                continue;

            $output->writeLn("Traitement fichier ".$file." at ".date('H:i:s'));
            // TODO => Change user/pass with config.yaml
            exec('mongo 127.0.0.1:27017/satisfactory -u satisfactory -p fe1gFu7fZJkwK3H '.$this->_out_folder.$file);
            sleep(1);

            rename($this->_out_folder . $file, $this->_out_folder . 'archives/' . $file);
        }
    }

}