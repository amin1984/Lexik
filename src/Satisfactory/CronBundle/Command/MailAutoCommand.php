<?php

namespace Satisfactory\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Satisfactory\CronBundle\Repository\MimeMailParser;

class MailAutoCommand extends ContainerAwareCommand {

    protected $_in_folder;
    protected $_out_folder;

    protected function configure() {
        $this->setName("cron:triggerMail")
        ->setDescription("Extract attached files from email");
    }

    protected function execute(InputInterface $input, OutputInterface $output) 
    {

        $Parser = new MimeMailParser();

        $Parser->setStream(fopen('php://stdin', 'r'));    

        $attachments = $Parser->getAttachments();

        $save_dir = "/home/www/www.celaneo.com/subdomains/etl_satisfactory/httpdocs/web/uploads/cron/";

        foreach($attachments as $k=>$attachment) {

            $filename = $attachment->filename;
            // write the file to the directory you want to save it in
            if ($fp = fopen($save_dir.$filename, 'w')) {
                while($bytes = $attachment->read()) {
                    fwrite($fp, $bytes);
                }
                fclose($fp);
            }
            chmod($save_dir.$filename, 0775);
        }    
    }     
}
