<?php

namespace Satisfactory\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Satisfactory\SettingBundle\Entity\Setting;
use Satisfactory\SettingBundle\Entity\Segment;
use Satisfactory\SettingBundle\Entity\Quest;
use Satisfactory\SettingBundle\Entity\Agency;
use Satisfactory\SettingBundle\Entity\AppParam;

class SettingCronCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName("cron:updatesetting")
            ->setDescription("Import js file in mongo db");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->getByDateBetween();
        
        /*because AppParam must contain only one row, so we query with find()
         * If AppParam change then we have to change the below line to get the 
         * correct parameters
         */
        $AppParam = $em->getRepository('SatisfactorySettingBundle:AppParam')->find(1);
        if(!$AppParam)
            return false;
        if($AppParam->getIdParamErdf() == "") {
            $output->writeLn('Erreur : nom tdc manquant');
            return false;
        }
        
        $kernel->getContainer();
        $m = $kernel->getContainer()->get('doctrine_mongodb.odm.default_connection');
        // select a database
        $db = $m->selectDatabase('satisfactory');
        $collection = $db->selectCollection($AppParam->getIdParamErdf());
        if(!$collection) {
            $output->writeLn('Erreur : impossible de créer la collection dans mongo');
            return false;
        }
        //empty the collection
        $collection->remove(array());
        $output->writeLn('COUNT SETTINGS : '.count($settings));
        if($settings) {
            foreach ($settings as $setting) {
                 $agencies = $setting->getAgency();
                 $segments = $setting->getSegment();
                $output->writeLn(($setting->getId()));
                 if($agencies) {
                     foreach ($agencies as $agency) {
                            $output->writeLn('--'.($agency->getId()));
                         if($segments) {
                            foreach ($segments as $segment) {
                                $output->writeLn('----'.($segment->getId()));
                                $values = array(
                                    'agency' => $agency->getId(),
                                    'segment_client' => $segment->getId(),
                                    'type_enquete' => $setting->getQuest()->getId(),
//                                    'userId' => $setting->getUser()->getId(),
                                    'name' => $setting->getName(),
                                    'date_debut' => $setting->getDateBegin()->format('Y-m-d H:i:s'),
                                    'date_fin' => $setting->getDateEnd()->format('Y-m-d H:i:s'),
                                    'groupId' => $setting->getGroupId(),
                                    'activation_enquete' => 1,
                                    );
                                $collection->insert($values);
//                                
//                                $paramGroup = new ParamGroupes();
//                                $paramGroup->setAgency($agency->getId());
//                                $paramGroup->setSegment($segment->getId());
//                                $paramGroup->setQuest($setting->getQuest()->getId());
//                                $paramGroup->setUserId($setting->getUser()->getId());
//                                $paramGroup->setName($setting->getName());
//                                $paramGroup->setBeginAt($setting->getDateBegin()->format('Y-m-d H'));
//                                
//                                $paramGroup->setGroupId($setting->getGroupId());
//                                $dm->persist($paramGroup);
//                                $dm->flush();
                            }
                        }
                     }
                 }
            }
        }
    }

}