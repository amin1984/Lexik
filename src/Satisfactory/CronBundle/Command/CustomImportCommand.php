<?php

namespace Satisfactory\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Symfony\Component\HttpFoundation\Request;
use Satisfactory\CronBundle\Repository\CronRepository;
use Satisfactory\CronBundle\Repository\SFTP;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Satisfactory\CronBundle\Entity\Cronexecution;
use Satisfactory\CronBundle\Entity\Manualexecutedealing;
use Satisfactory\OperationBundle\Entity\Dealing;
use Satisfactory\OperationBundle\Entity\Operation;
use Satisfactory\OperationBundle\Entity\Reject;
use Doctrine\Common\Collections\ArrayCollection;

use Satisfactory\CronBundle\Document\RejectExecution;
use Satisfactory\CronBundle\Document\RejectLog;
use Satisfactory\OperationBundle\Document\Correspondence;
use Satisfactory\OperationBundle\Document\EnterCorrespondence;
use Sinner\Phpseclib\Net\Net_SFTP;

class CustomImportCommand extends ContainerAwareCommand
{
    private $em;
    private $currentExecution;

    protected function configure()
    {
        $this->setName("staisfactory:import")
        ->setDescription("Import custom file");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $em = $this->getContainer()->get('doctrine')->getManager();
        //$count = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findCountByCorrespondence(49);
        $this->manualexecuteAction(191);
        //*
        /*

        $correspondance = $em->getRepository('SatisfactoryOperationBundle:Correspondance')->find(37);
        
                $filename = __DIR__.'/phpXpeI2h_Table_Geo_INSEE.csv';
  
                $documents = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findByCorrespondence($correspondance->getId());
                if($documents) {
                    foreach ($documents as $d) {
                        $dm->remove($d);
                    }
                    $dm->flush();
                }

                $this->saveEnters($correspondance,$filename);
        



        $count = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findCountByCorrespondence(37);*/
        echo "DONE";
        return;



        $countQuery = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->createQueryBuilder();

        $countQuery->requireIndexes(false);

        $count = $countQuery->count()->getQuery()->execute();
        //echo "COUNT :".$count;

        $offset         = 0;
        $threshold      = 5000;
        $fullThreshold  = 50000;
        $allKeys        = array();
        $dbl = 0;
        while( $count >= $offset )
        {
            $startTime  = microtime(true);
            $tempOffset = 0;
            $computed   = array();
            $toRemove   = array();
            while( $fullThreshold >= $tempOffset && $count >= $offset)
            {
                $entries = null;
                $qb = $dm->createQueryBuilder('SatisfactoryOperationBundle:EnterCorrespondence');
                $qb->hydrate(false)->skip($offset)->limit($threshold);


                $entries = $qb->getQuery()->execute();

                foreach($entries as $entry)
                {
                    if( isset($entry['input']['PDS_ID']))
                    {
                        if( !isset($allKeys[$entry['input']['PDS_ID']]) )
                        {
                            $allKeys[$entry['input']['PDS_ID']] = true;
                        }
                        else
                        {
                            ++$dbl;

                            $toRemove[] = $entry['_id'];

                            //print_r($entry);
                            //echo 'DOUBLE : '.$entry['_id']."\r\n";
                        }
                    }
                    /*foreach($entry['input'] as $key => $input)
                    {
                    if( !isset($computed[$key]) )
                    {
                    $computed[$key] = true;
                    }
                    } */
                }

                unset($entry);
                unset($entries);
                $offset     += $threshold;
                $tempOffset += $threshold;
                //echo 'LOADED '.$offset." of $count\r\n";

            }   

            foreach($computed as $key => $bol)
            {
                if(!isset($allKeys[$key]))
                {
                    $allKeys[$key] = true;
                }
            }

            //print_r($allKeys);
            gc_collect_cycles();
            //meminfo_size_info(fopen('php://stdout','w'));                        
            //meminfo_objects_summary(fopen('php://stdout','w'));
            //meminfo_objects_list(fopen('php://stdout','w'));
            echo 'COMPUTED '.$offset." OF ".$count.", ".$dbl." TWICE. (".(round(memory_get_usage(true)/1048576,2))." megabytes".")\r\n";
            $startTime = microtime(true);
            /*$qb = $dm->createQueryBuilder('SatisfactoryOperationBundle:EnterCorrespondence');
            $qb->remove()
            ->field('id')->in($toRemove)
            ->getQuery()
            ->execute();

            $dm->flush();
            $toRemove = array();*/


        }

        print_r($allKeys);

        echo 'CASE 1 - COUNT DONE'."\r\n";     
        echo 'CASE 1 - COUNT CORRES : '."$count\r\n";
        echo 'CASE 1 - COUNT LINES  : '.count($fileContent)."\r\n";

        // Precompute if preg_match needed !

        // Precompute correspondances

        $done           = array();
        $repo           = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence');
        gc_enable();



        unset($computed);


    }

    public function shutdownHandler()
    {
        if( is_object($this->currentExecution) && is_object($this->em) )
        {
            $this->currentExecution->setRunning(false);
            $this->em->persist($this->currentExecution);
            $this->em->flush();    
        }
        exit;
    }

    public function errorHandler()
    {
        if( is_object($this->currentExecution) && is_object($this->em) )
        {                                                         
            $this->currentExecution->setRunning(false);
            $this->em->persist($this->currentExecution);
            $this->em->flush();    
        }
    }

    /**
    * Launch the main cron process.
    *
    */
    public function manualexecuteAction($id = null)         
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dealing = $em->getRepository('SatisfactoryOperationBundle:Dealing')->find($id);

        if(!$dealing) {
            /*$this->getRequest()->getSession()
            ->getFlashBag()
            ->add('cron_manual_execute_error', $this->getContainer()->get('translator')->trans('Traitement manuel fini.'))
            ; */
            return;// $this->redirectToRoute('operation_dealing_index');
        }
        $log = $this->executeDealing($dealing);

        /*$this->getRequest()->getSession()
        ->getFlashBag()
        ->add('cron_manual_execute_success', $this->getContainer()->get('translator')->trans('Traitement manuel fini.'))
        ; */
        return;// $this->redirectToRoute('operation_dealing_index');
    }


    /**
    * Launch the main cron process.
    *
    */
    public function indexAction()
    {
        //$currentTimeCronExecution = time();
        $currentTimeCronExecution = time();
        $dealings = $this->getExecutableDealings($currentTimeCronExecution);
        /******************************************************************************
        * ****************************************************************************
        *  TODO : put a try catch bloc to catch php errors to be surethat the 
        * time of begin and end of the execution is saved
        ******************************************************************************
        * ****************************************************************************/
        if($dealings) {
            foreach ($dealings as $dealing) {
                $log = $this->executeDealing($dealing);
                // log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('Fin execution traitement #%id% %link%',array('%id%'=>$dealing->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_dealing_edit', array('id' => $dealing->getId()))));
                    $this->logEndDealing($log,$message,$dealing);
                }
                /*echo "<pre>";
                print_r($log->getLog());
                echo "</pre>";          */
                $this->sendMailNotifications($log);
            }
        }
        /*
        * test code to remove after dev
        * 
        $em = $this->getContainer()->get('doctrine')->getManager();
        $d = $em->getRepository('SatisfactoryOperationBundle:Dealing')->findOneById(48);
        if($this->executeDealing($d)) {
        // log here
        if($this->getDebugMode()) {
        $message = $this->getContainer()->get('translator')->trans('Fin execution traitement #%id%',array('%id%'=>$d->getid()));
        //$this->saveLog($log,$message);
        }
        $this->sendMailNotifications($d,"2016-03-31 11:59:19");
        }*/
        return '';//$this->render('SatisfactoryCronBundle:Default:index.html.twig');
    }

    /*
    * get the list of all Dealings that can be executed 
    */
    protected function getExecutableDealings($currentTimeCronExecution) 
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dealings = $em->getRepository('SatisfactoryOperationBundle:Dealing')->findBy(array('isActive' => 1));

        if(!$dealings) {
            return false;
        }
        $executableDealing = array();
        foreach ($dealings as $dealing) {
            switch ($dealing->getRecurence()) {
                case 1 :
                    if(in_array(date('N',$currentTimeCronExecution), $dealing->getDays())) {
                        if($dealing->getTimeDay()->format('H:i') == date('H:i',$currentTimeCronExecution)) {
                            $executableDealing[] = $dealing;
                        }
                    }
                    break;
                case 2 :
                    if($dealing->getDayOfMonth() == date('d',$currentTimeCronExecution)) {
                        if($dealing->getTimeMonth()->format('H:i') == date('H:i',$currentTimeCronExecution)) {
                            $executableDealing[] = $dealing;
                        }
                    }
                    break;
                case 3 :
                    $executableDealing[] = $dealing;
                    break;
            }
        }
        $manualDealings = $em->getRepository('SatisfactoryCronBundle:Manualexecutedealing')->findAll();
        if($manualDealings) {
            foreach ($manualDealings as $manualDealing) {
                $executableDealing[] = $manualDealing->getDealing();
                $em->remove($manualDealing);
            }
            $em->flush();
        }
        return $executableDealing;
    }
    protected function IsDealingExecuting($dealing,$initTimeCron) 
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $c  = $em->getRepository('SatisfactoryCronBundle:Cronexecution')->findBy(array('dealing' => $dealing,'endAt'=>NULL, 'running' => true));

        if (count($c) > 0) 
        {
            echo "DEALING ".$dealing->getId().' IS RUNNING - STOPPING.'."\r\n";
            return true;
        }

        return FALSE;
    }


    protected function executeDealing($dealing = null) 
    {
        if (!$dealing) {
            return false;
        }
        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->em = $em;
        $initTime = time();
        $dateInitexecution  = date('Y.m.d.H.i.s',$initTime);
        $initTimeCron       = date('Y-m-d H:i:s',  $initTime);

        /*if($this->IsDealingExecuting($dealing,$initTimeCron))
        {
        return false;
        }    */



        //log here
        $log = null;
        if($this->getDebugMode()) 
        {
            $log = $this->logBeginDealing($dealing,$this->getContainer()->get('translator')->trans('Debut execution traitement #%id% : %title% %link%', array('%id%' => $dealing->getId(),'%title%' => $dealing->getName(),'%link%'=> $this->getContainer()->get('router')->generate('operation_dealing_edit', array('id' => $dealing->getId())))),'success',$initTimeCron);
        }

        $this->currentExecution = $log;

        $tmpData = $this->cloneFileFromFtp($dealing,$this->getContainer()->getParameter('cronTmpDir'),$dateInitexecution,$log);

        if (!$tmpData)  
        {
            //log here
            if($this->getDebugMode()) 
            {
                $message = $this->getContainer()->get('translator')->trans('Erreur copie fichier original');
                $this->saveLog($log,$message,'error');
            }
            return $log;
        }

        $f = "";
        if($tmpData) {
            if(isset($tmpData["tmpDir"]) && isset($tmpData["tmpFileName"])) {
                $f = $tmpData["tmpDir"].$tmpData["tmpFileName"];
            }
        }

        $headerFromFile = array();
        if($dealing->getFileHeader() && $dealing->getIsFileHeader() == 1)
        {

            $headerFromFile = explode (";", $dealing->getFileHeader());
        }
        $separator  =  CronRepository::GetCsvSeparatorFromDealing($dealing);

        $fileErrors = CronRepository::csvCkeck($f, $separator,$headerFromFile,$dealing,false);

        // if the file contain somm errors in some rows, then we put that in LOG with status WARNING
        if($fileErrors) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Fichier contient des erreurs #%lines% : %file%',array('%lines%'=>$fileErrors,'%file%'=>  $f));
                $this->saveLog($log,$message,'warning');
            }
        }

        if($dealing->getNbLigneToDelete() > 0) {
            //delete X column from the original file
            $countLines = 1;
            $handle = fopen($f, "r");
            if ($handle) {
                $lines = file($f);
                if($lines) {
                    for ($i = 0;$i < $dealing->getNbLigneToDelete();$i++) {
                        unset($lines[$i]);
                    }
                }
                array_values($lines);
                file_put_contents($f, $lines);
                fclose($handle);
            } else {
                //log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('Erreur : imposible de supprimer les %lines% lignes du fichier original du traitement',array('%lines%'=>$dealing->getNbLigneToDelete()));
                    $this->saveLog($log,$message,'error');
                }
                return $log;
            }

        }




        if($dealing->getDeleteSemicolon() > 0) {
            // if "suppression du ; final dans l'en-tete" is checked then we have to delete it from the first line of the file (from the header)

            $lines = file($f);
            $lines[0] = preg_replace( "/\r|\n/", "", $lines[0] );
            if(substr($lines[0], -1) ==';')
                $lines[0] = substr($lines[0],0, -1);

            $lines[0].="\n";
            file_put_contents($f, $lines);
        }

        if($dealing->getIsFileheader() > 0) {
            //  if the file does not contain a header so we must take the colums and insert it to the file as a header
            $headerFile = explode(";", $dealing->getFileheader());
            $headerFile = implode(CronRepository::GetCsvSeparatorFromDealing($dealing), $headerFromFile);
            $headerFile.="\n";
            $headerFileArray[] = $headerFile;

            $lines = file($f);
            $lines = array_merge($headerFileArray,$lines);
            file_put_contents($f, $lines);

        }
        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Copie fichier original du traitement #%id% : %file%',array('%id%'=>$dealing->getid(),'%file%'=>  $f));
            $this->saveLog($log,$message);
        }


        $orderedOperations = $em->getRepository('SatisfactoryOperationBundle:Operation')->getAcitfOrderOperations($dealing);
        if(!$orderedOperations) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Tratement ne contient pas des operations %id% %link%',array('%id%'=>$dealing->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_dealing_edit', array('id' => $dealing->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return $log;
        }
        $operationNum = 1;
        $operationCount = count($orderedOperations);
        foreach ($orderedOperations as $operation) {
            if($operationNum > 1) {
                $tmpData['tmpFileName'] = $operationFilename;
            }
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Debut execution operation %id% %num% sur %count% %link%',array('%id%'=>$operation->getid(),'%num%'=>  $operationNum,'%count%'=>  $operationCount,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message);
            }
            echo "OPERATION NUMBER : ".$operation->getId()."\r\n";
            $operationFilename=$this->executeOperation($dealing,$operation,$tmpData,$operationNum,$log);
            if(!$operationFilename) {
                // log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('Echec operation %id% %num% sur %count% %link%',array('%id%'=>$operation->getid(),'%num%'=>  $operationNum,'%count%'=>  $operationCount,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message,'error');
                }
                return $log;
            }

            $operationNum++;
        }

        return $log;
    }

    protected function executeOperation($dealing,$operation,$tmpData,$operationNum,$log = null) 
    {
        if(!$operation) {

            return false;
        }
        $resultFile = "";
        switch ($operation->getType()) {
            case "Filter" : //Filter
                $resultFile = $this->executeOperationFiltrer($dealing,$operation,$tmpData,$operationNum,$log);
                break;
            case "Modify" : //Modifier la structure
                $resultFile = $this->executeOperationModifierStructure($dealing,$operation,$tmpData,$operationNum,$log);

                break;
            case "Enrich" : //Enrichir
                $resultFile = $this->executeOperationEnrichir($dealing,$operation,$tmpData,$operationNum,$log);
                break;
            case "Dedoublonner" : //Dedoublonne
                $resultFile = $this->executeOperationDedoublonner($dealing,$operation,$tmpData,$operationNum,$log);
                break;
            case "Rejected" : //Rejeter
                $resultFile = $this->executeOperationRejeter($dealing,$operation,$tmpData,$operationNum,$log);

                break;
            case "Rule" : //Regle de filtrage client
                $resultFile = $this->executeOperationFiltrageClient($dealing,$operation,$tmpData,$operationNum,$log);

                break;
            case "Table" : //Table de correspondance
                $resultFile = $this->executeOperationTableCorrespondance($dealing,$operation,$tmpData,$operationNum,$log);

                break;
            case "Publish" : //Publier
                $resultFile = $this->executeOperationPublier($dealing,$operation,$tmpData,$operationNum,$log);
                break;
            case "Archive" : //Archiver
                $resultFile = $this->executeOperationArchiver($dealing,$operation,$tmpData,$operationNum,$log);
                break;
        }
        return $resultFile;
    }

    protected function executeOperationDedoublonner($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);

        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id%',array('%id%'=>$operation->getid(),'%file%'=>$filename));
            $this->saveLog($log,$message);
        }

        $column = $operation->getDuplicateNameColumn();
        $hasColumn = false;
        foreach ($fileContent[0] as $key =>$val) {
            if($column == $key) {
                $hasColumn = true;
            }
        }
        if (!$hasColumn) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        //array that contain all unique vlaues of the given column
        $valuesOfGivenColumn = array();

        foreach ($fileContent as $row) {

            if(is_array($row)) {
                if (array_key_exists($column, $row)) {
                    $valuesOfGivenColumn[] = $row[$column];
                } 
            }
        }
        $valuesOfGivenColumn = array_unique($valuesOfGivenColumn);
        if(!$valuesOfGivenColumn) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Pas de valurs dans la colone "%column%" pour l\'operation %id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        // Search the duplicated values and remove them from the hole array
        foreach ($valuesOfGivenColumn as $v) {
            $keys = array(); #array that will contain all keys from the array that contain that value
            foreach ($fileContent as $k=>$row) {
                if(is_array($row)) {
                    if (array_key_exists($column, $row)) {
                        if($row[$column] == $v) {
                            $keys[] = $k;
                        }
                    } 
                } 
            }
            if($keys && count($keys)>1) {
                $keepWhat = $operation->getDuplicateKeep();
                if($keepWhat == 1) {
                    for($i=1;$i<count($keys);$i++) {
                        unset($fileContent[$keys[$i]]);
                    }
                }
                elseif($keepWhat == 2) {
                    for($i=0;$i<count($keys)-1;$i++) {
                        unset($fileContent[$keys[$i]]);
                    }
                }
            }
        }
        //reset the keys of the array
        $fileContent = array_values($fileContent);
        $fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;
        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        foreach ($list as $fields) {
            foreach ($fields as $k => $field) {
                //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
            }
            if($dealing->getQuotation()=="oui")
                fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
            else  {
                $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                $fields.="\n";
                fputs($fp, $fields);
            }
        }

        fclose($fp);
        return $fileTmpName;


    }



    protected function executeOperationFiltrer($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);
        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
            $this->saveLog($log,$message);
        }


        $header= array() ;
        foreach ($fileContent[0] as $k=>$value) {
            $header[$k] = "";
        }
        $filtre = $operation->getJson();
        if(!$filtre) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : l\'operation %id% ne contient pas un filtre %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $filtre = json_decode($filtre);
        foreach ($fileContent as $k => $row) {
            var_dump($this->executeFilterJson($filtre, $row));
            echo '<br />';
            if(!$this->executeFilterJson($filtre, $row)) {
                unset($fileContent[$k]);
            }
        }
        if(!$fileContent) 
            $fileContent[] = $header;

        $fileContent = array_values($fileContent);
        $fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;
        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        if($list) {
            foreach ($list as $fields) {
                foreach ($fields as $k => $field) {
                    //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                    $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                }
                if($dealing->getQuotation()=="oui")
                    fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                else  {
                    $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                    $fields.="\n";
                    fputs($fp, $fields);
                }
            }
        }
        fclose($fp);
        return $fileTmpName;
    }


    protected function filterByRow($rule,$fileContent, $row = array()) 
    {
        /***************************************************************************/
        /* $rule->value can be string or array (array when the operator is "between")
         * so we have to add test id $rule->value is string or array */
        /***************************************************************************/
        $string     = $rule->value;
        $ruleSave   = $string;
        if(is_array($string)) {
            foreach ($string as $k=>$str) {
                if( strpos($str, '[') !== FALSE )
                {

                    if( !isset($rule->preg_columns) )
                    {                           
                        preg_match_all("/(?<=\[).+?(?=\])/", $rule->value, $columns);    

                        if( isset($columns) )
                        {
                            $rule->preg_columns = $columns;
                        }
                    } 


                    //apply rule  for all rows
                    if(isset($rule->preg_columns[0])) 
                    {

                        foreach ($rule->preg_columns[0] as $c) 
                        {
                            if(empty($row)) 
                            {
                                if(isset($fileContent[$c]))
                                    $str = str_replace('['.$c.']',$fileContent[$c],$str);
                            }
                            else 
                            {
                                if(isset($row[$c]))
                                $str = str_replace('['.$c.']',$row[$c],$str);
                            }
                        }
                    }
                    $str = CronRepository::convertHorodate($string);
                    $string[$k] = $str;
                }    
            }
        }
        else {
            if( strpos($string, '[') !== FALSE )
            {

                if( !isset($rule->preg_columns) )
                {                           
                    preg_match_all("/(?<=\[).+?(?=\])/", $rule->value, $columns);    

                    if( isset($columns) )
                    {
                        $rule->preg_columns = $columns;
                    }
                } 


                //apply rule  for all rows
                if(isset($rule->preg_columns[0])) 
                {

                    foreach ($rule->preg_columns[0] as $c) 
                    {
                        if(empty($row)) 
                        {
                            if(isset($fileContent[$c]))
                                $string = str_replace('['.$c.']',$fileContent[$c],$string);
                        }
                        else 
                        {
                            if(isset($row[$c]))
                            $string = str_replace('['.$c.']',$row[$c],$string);
                        }
                    }
                }
                $string = CronRepository::convertHorodate($string);
            }    
        }
                                                         

        $rule->value = $string;
        $return      = false;
        if(!isset($fileContent[$rule->field]))
            return false;
        
        $fileContent[$rule->field] = trim($fileContent[$rule->field]);
        
        switch($rule->operator) {
            case "equal" : // Egal a une valeur
            
                echo "***** TESTING *****\r\n";
                echo '===> FROM FILE '.@$fileContent[$rule->field]." AGAINST RULE ".$rule->value." : ";
                
                
                $return = (strtolower($rule->value) == strtolower(@$fileContent[$rule->field]));
                
                if( $return )
                {
                    echo " ____  VALIDATED AGAINST ".$rule->value."\r\n";
                }
                else
                {
                    echo " !!!!!! INVALID AGAINST ".$rule->value."\r\n"; 
                }
                
                break;
            case "not_equal" : // Different d\’une valeur 
                $return = (strtolower($rule->value) != strtolower(@$fileContent[$rule->field]));
                break;
            case "greater_or_equal" : // superieur ou egal a une valeur
                $return = (strnatcmp(strtolower(@$fileContent[$rule->field]),strtolower($rule->value)) >= 0);
                break;
            case "less_or_equal" : //  Inferieur ou egal a une valeur
                $return = (strnatcmp(strtolower(@$fileContent[$rule->field]),strtolower($rule->value)) <= 0);
                break;
            case "between" : // Compris entre 2 valeurs
                $return = ((strnatcmp(strtolower(@$fileContent[$rule->field]),strtolower($rule->value[0])) >= 0) && (strnatcmp(strtolower(@$fileContent[$rule->field]),strtolower($rule->value[1]) <= 0)));
                break;
            case "is_empty" : // valeur vide
                $return = @$fileContent[$rule->field] == "";
                break;
            case "is_not_empty" : // valeur non vide
                $return = @$fileContent[$rule->field] != "";
                break;
            case "begins_with" : // wildcards
                $expressions = explode(';',$rule->value);
                
                if($expressions) {
                    foreach ($expressions as $expression) {
                        $valid = CronRepository::executewildcards(strtolower(@$fileContent[$rule->field]), strtolower($expression));
                        
                        if($valid) {
                            
                            $rule->value = $ruleSave;
                            return($valid);
                        }
                        else
                        {
                            
                        }
                    }
                }
                echo "***** TESTING END *****\r\n";
                $return = FALSE;
                break;
        }
        $rule->value = $ruleSave;
        return $return;
    }

    protected function precomputeExecuteFilterJson($element) {

        if(isset($element->condition) && isset($element->rules)) 
        {
            foreach ($element->rules as $rule) 
            {
                if(isset($rule->condition) && isset($rule->rules)) 
                {
                    if($element->condition == "AND")
                        $this->precomputeExecuteFilterJson($rule);
                    else
                        $this->precomputeExecuteFilterJson($rule);
                }
                else 
                {
                    if($element->condition == "AND")
                        $this->precomputeFilterByRow($rule);
                    else
                        $this->precomputeFilterByRow($rule);
                }
            }

        }
    }

    function precomputeFilterByRow($rule, $fileContent, $row)
    {
        if(is_array($rule->value)) {
            foreach ($rule->value as $k =>$str) {
                if( strpos($str, '[') !== FALSE )
                {
                    preg_match_all("/(?<=\[).+?(?=\])/", $str, $columns);    

                    if( isset($columns[0]) )
                    {
                        $rule->preg_columns = $columns[0];
                        $rule->needParsing  = true;    
                    }
                    else
                    {
                        $rule->needParsing = false;
                    }
                }
                else
                {
                    $rule->needParsing = false;
                }
            }
        }else {
        if( strpos($rule->value, '[') !== FALSE )
        {
            preg_match_all("/(?<=\[).+?(?=\])/", $rule->value, $columns);    

            if( isset($columns[0]) )
            {
                $rule->preg_columns = $columns[0];
                $rule->needParsing  = true;    
            }
            else
            {
                $rule->needParsing = false;
            }
        }
        else
        {
            $rule->needParsing = false;
        }
            
        }
    }


    protected function executeFilterJson($element,$fileContent,$row = array()) {
        $return = true;
        if(isset($element->condition) && isset($element->rules)) 
        {
            if($element->condition == "OR")
                $return = FALSE;
            foreach ($element->rules as $rule) 
            {
                if(isset($rule->condition) && isset($rule->rules)) 
                {
                    if($element->condition == "AND")
                        $return = $return &&  $this->executeFilterJson($rule,$fileContent,$row);
                    else
                        $return = $return ||  $this->executeFilterJson($rule,$fileContent,$row);
                }
                else 
                {
                    if($element->condition == "AND")
                        $return = $return && $this->filterByRow($rule,$fileContent,$row);
                    else {
                        $return = $return || $this->filterByRow($rule,$fileContent,$row);
                    }
                }
            }

        }

        return $return;
    }

    protected function executeOperationModifierStructure($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }        
        switch($operation->getModifyStructure()) {
            case 1: //Renommer
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                $column = $operation->getModifyNameColumnToRename();
                $fileContent = CronRepository::genrateCsvFormat($fileContent);
                if(!$fileContent || !is_array($fileContent[0])) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                if (!in_array($column, $fileContent[0])) {
                    // log here error column not found
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                else {
                    foreach ($fileContent[0] as $k=>$v) {
                        if($fileContent[0][$k]==$column)
                            $fileContent[0][$k] = $operation->getModifyNameColumnRename();
                    }
                }

                break;

            case 2: //Trier les lignes
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    // log here fail 
                    return false;
                }

                $column = $operation->getModifyNameColumnToSort();
                if (!array_key_exists ($column, $fileContent[0])) {
                    // log here error column not found
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                else {
                    $simpleArrayToOrder = array();
                    foreach($fileContent as  $key=>$row) {
                        if(!isset($row[$column])) {
                            // log here the column does not exist in one of the lines in the csv
                            return false;
                        }
                        $simpleArrayToOrder[$key] = $row[$column];
                    }

                    if ($operation->getModifyTypeSort()==1)
                        asort($simpleArrayToOrder,SORT_STRING);
                    else
                        arsort($simpleArrayToOrder,SORT_STRING);
                    $resultArray=array();
                    foreach($simpleArrayToOrder as $key =>$val) {
                        $resultArray[] = $fileContent[$key];
                    }
                    $fileContent = $resultArray;
                    $fileContent = CronRepository::genrateCsvFormat($fileContent);

                }
                break;
            case 3: //Ajouter
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    // log here fail 
                    return false;
                }

                $newColumn = explode(";", $operation->getModifyNameColumnToAdded());
                $resultArray = array();
                if($operation->getModifyAddedPosition()==1) {
                    if($newColumn) {
                        $arr = array();
                        foreach ($newColumn as $col)
                            $arr[$col] = "";
                        foreach($fileContent as $row) {
                            $resultArray[] = array_merge($arr, $row);
                        }
                        $fileContent = $resultArray;
                        $fileContent = CronRepository::genrateCsvFormat($fileContent);
                    }
                }
                else {
                    $column = $operation->getModifyNameColumnposition();
                    if (!array_key_exists ($column, $fileContent[0])) {
                        // log here error column not found
                        // log here
                        if($this->getDebugMode()) {
                            $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                            $this->saveLog($log,$message,'error');
                        }
                        return false;
                    }

                    foreach($fileContent as $k => $row) {

                        if($newColumn) {
                            $newColumn = array_reverse($newColumn);
                            foreach ($newColumn as $col)
                                $row = CronRepository::arrayIinsertAfter($column, $row, $col, '');
                            $fileContent[$k] = $row;
                        }
                    }
                    $fileContent = CronRepository::genrateCsvFormat($fileContent);

                }
                break;
            case 4: //Supprimer
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    // log here fail 
                    return false;
                }

                $column = explode(";", $operation->getColumnToDelete());
                $resultArray = array();
                foreach($fileContent as $k => $row) {
                    if($column) {
                        foreach ($column as $col) {
                            if(isset($row[$col]))
                                unset($row[$col]);
                        }
                        $fileContent[$k] = $row;
                    }
                }
                $fileContent = array_values($fileContent);
                $fileContent = CronRepository::genrateCsvFormat($fileContent);

                break;
            case 5: //Reordonner
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    // log here fail 
                    return false;
                }

                $column = $operation->getNameColumnPosition();
                $resultArray = array();
                $resultArray1 = array();
                $retreivedColumValues = array();
                foreach($fileContent as $k => $row) {
                    $retreivedColumValues[$k] = array($column => $row[$column]);
                    unset($row[$column]);
                    $resultArray[] = $row;
                }
                $fileContent = $resultArray;
                if($operation->getReorderPosition()==1) {
                    foreach($fileContent as $k => $row) {
                        $resultArray1[] = array_merge($retreivedColumValues[$k], $row);
                    }
                    $fileContent = $resultArray1;
                    $fileContent = CronRepository::genrateCsvFormat($fileContent);
                }
                else {
                    $columnAfter = $operation->getReorderColumnName();
                    if (!array_key_exists ($columnAfter, $fileContent[0])) {
                        // log here error column not found
                        // log here
                        if($this->getDebugMode()) {
                            $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                            $this->saveLog($log,$message,'error');
                        }
                        return false;
                    }

                    foreach($fileContent as $k=>$row) {
                        $resultArray1[] = CronRepository::arrayIinsertAfter($columnAfter, $row, $column, $retreivedColumValues[$k][$column]);
                    }
                    $fileContent = $resultArray1;
                    $fileContent = CronRepository::genrateCsvFormat($fileContent);

                }
                break;
            case "6": //Modifier le format d'une colonne
                $fileContent = CronRepository::parseCSVFile($filename, $dealing);
                if(!$fileContent) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    // log here fail 
                    return false;
                }

                $column = $operation->getNameColumnFormat();
                if (!array_key_exists ($column, $fileContent[0])) {
                    // log here error column not found
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                switch ($operation->getColumnFormat()) {
                    case 1: //Format telephone
                        if($operation->getTargetFormatPhone() == 1) { //Transposer au format téléphonique international 
                            foreach($fileContent as $k => $row) {
                                if(in_array(substr($row[$column],0,2), array('01','02','03','04','05','05','06','07','08','09'))) {
                                    $row[$column] = "+".$operation->getCountryCode().substr($row[$column],1,  strlen($row[$column]));
                                    $fileContent[$k] = $row;
                                }
                            }
                        }
                        if($operation->getTargetFormatPhone() == 2) { //Transposer dans un format manuel
                            foreach($fileContent as $k => $row) {
                                $newFormat = $operation->getNewFormat();
                                $numbers = str_split(filter_var($row[$column], FILTER_SANITIZE_NUMBER_INT));
                                if($numbers) {
                                    foreach ($numbers as $num) {
                                        $newFormat = CronRepository::str_replace_first('X', $num, $newFormat);
                                    }
                                    $row[$column] = $newFormat;
                                    $row[$column] = str_replace('X', '', $row[$column]);
                                    $fileContent[$k] = $row;
                                }
                            }
                        }
                        break;

                    case 2: //Format date
                        $dateSource         = $operation->getSourceFormatDate();
                        $dateTargetOriginal = $operation->getTargetFormatDate();
                        foreach($fileContent as $k => $row) {
                            $dateTarget = $dateTargetOriginal;
                            $posAAAA = strpos( $dateSource,'AAAA');
                            $posAA = strpos( $dateSource,'AA');
                            $posJJ = strpos( $dateSource,'JJ');
                            $posMM = strpos( $dateSource,'MM');
                            $posHH = strpos( $dateSource,'HH');
                            $posII = strpos( $dateSource,'II');
                            $posSS = strpos( $dateSource,'SS');
                            $posX = strpos( $dateSource,'X');
                            if($posAAAA !== FALSE) {
                                $dateTarget = str_replace ('AAAA',  substr($row[$column], $posAAAA, strlen('AAAA')) , $dateTarget);
                            }
                            if($posAA !== FALSE)
                                $dateTarget = str_replace ('AA',  substr($row[$column], $posAA, strlen('AA')) , $dateTarget);
                            if($posMM !== FALSE)
                                $dateTarget = str_replace ('MM',  substr($row[$column], $posMM, strlen('MM')) , $dateTarget);
                            if($posJJ !== FALSE)
                                $dateTarget = str_replace ('JJ',  substr($row[$column], $posJJ, strlen('JJ')) , $dateTarget);
                            if($posHH !== FALSE)
                                $dateTarget = str_replace ('HH',  substr($row[$column], $posHH, strlen('HH')) , $dateTarget);
                            if($posII !== FALSE)
                                $dateTarget = str_replace ('II',  substr($row[$column], $posII, strlen('II')) , $dateTarget);
                            if($posSS !== FALSE)
                                $dateTarget = str_replace ('SS',  substr($row[$column], $posSS, strlen('SS')) , $dateTarget);
                            if($posX !== FALSE)
                                $dateTarget = str_replace ('X',  substr($row[$column], $posX, strlen('X')) , $dateTarget);
                            $row[$column] = $dateTarget;
                            $fileContent[$k] = $row;
                        }


                        break;
                }
                $fileContent = array_values($fileContent);
                $fileContent = CronRepository::genrateCsvFormat($fileContent);

                break;
        }

        //$fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;
        echo "<pre>";
        print_r(CronRepository::GetCsvSeparatorFromDealing($dealing));
        echo "</pre>";
        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        foreach ($list as $fields) {
            foreach ($fields as $k => $field) {
                //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
            }
            if($dealing->getQuotation()=="oui")
                fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
            else  {
                /*$fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                $fields.="\n";
                fputcsv($fp, $fields, )
                fputs($fp, $fields);*/
                fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing));
            }
        }

        fclose($fp);
        return $fileTmpName;

    }


    protected function executeOperationPublier($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }

        if($operation->getPublishProtocol()=="sftp") {
            $ftp = @new Net_SFTP($operation->getPublishHost(),$operation->getPublishPort());
            $conn_ftp = @$ftp->login($operation->getPublishLogin(), $operation->getPublishPassword());
        }
        else {
            $ftp = new SFTP($operation->getPublishHost(),$operation->getPublishLogin() , $operation->getPublishPassword(),$operation->getPublishPort()); 
            $conn_ftp = $ftp->connect();
        }
        if (!$conn_ftp) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Impossible de se connecter au FTP');
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Connexion FTP etablie');
            $this->saveLog($log,$message);
        }
        $remoteFilename = CronRepository::convertHorodate($operation->getPublishFileName());

        $localFilename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        $ftpDirectory = $operation->getPublishDirectory();
        if(substr($ftpDirectory, -1) !='/')
            $ftpDirectory.='/';

        if($operation->getPublishProtocol()=="sftp") {
            if($ftp->put($ftpDirectory.$remoteFilename, file_get_contents($localFilename))) { 
                //log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('copie de fichier distant  operation #%id% %link%',array('id'=>$operation->getId(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message);
                }
                $this->logIfRejectsExist($localFilename,$dealing,$operation);
                return $tmpData;
            }
        }
        else {

            if(@$ftp->put($localFilename, $ftpDirectory.$remoteFilename)) { 
                //log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('copie de fichier distant  operation #%id% %link%',array('id'=>$operation->getId(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message);
                }
                $this->logIfRejectsExist($localFilename,$dealing,$operation);
                return $tmpData;
            }
        }
        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Erreur : impossible envoyer le fichier destination');
            $this->saveLog($log,$message,'error');
        }
        return $tmpData;



    }


    protected function executeOperationEnrichir($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        //echo 'ENRICH-1'."\r\n";
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        //echo 'ENRICH-2'."\r\n";
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);

        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        //echo 'ENRICH-3'."\r\n";
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id%',array('%id%'=>$operation->getid(),'%file%'=>$filename));
            $this->saveLog($log,$message);
        }
        //get the filter rules 
        $filtre = $operation->getJson();
        $filtre = json_decode($filtre);
        echo 'ENRICH-4-BEFORE'."\r\n";
        echo 'ENRICH-OP-'.$operation->getId()."\r\n";
        switch ($operation->getEnrichFilter()) {
            case 1 : // Appliquer une correspondance de valeurs sur une colonne :
                echo 'CASE 1 - BEGIN'."\r\n";
                $correspondance = $operation->getCorrespondance();
                //echo 'CASE 1 - CORRES'."\r\n";
                $columns = $correspondance->getColumns();
                //echo 'CASE 1 - COLUMN'."\r\n";
                echo 'CASE 1 - JSON DONE'."\r\n";
                if(!$columns) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : correspondance ne contient pas des colonnes %correspondance% %link%',array('%correspondance%'=>$correspondance->getName(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_correspondance_edit', array('id' => $correspondance->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                $columnOutput = $operation->getEnrichColumnName();
                //echo 'CASE 1 - ENRICH COLUMN NAME'."\r\n";
                $correspondanceOutput = $operation->getTargetColumn();
                //echo 'CASE 1 - TARGET COLUMN'."\r\n";
                $filtreCorrespondance = $operation->getJsonCorrespondence();
                //echo 'CASE 1 - JSON CORRES'."\r\n";
                $filtreCorrespondance = json_decode($filtreCorrespondance,true);
                // add the "Nom de la colonne du fichier source (CSV)" to the list of column. This is just to verify if this column exists in the CSV file.
                $columns[] = $columnOutput;
                // we must test if all columns in the correspondance already exists in the file. If not then generate a log error
                //                $missedColumns = array();
                //                foreach ($columns as $c) {
                //                    if(!array_key_exists($c,$fileContent[0])) {
                //                        $missedColumns[] = $c;
                //                    }
                //                }
                //                if($missedColumns) {
                //                    // log here
                //                    if($this->getDebugMode()) {
                //                        $message = $this->getContainer()->get('translator')->trans('Erreur : Les colonnes : %columns% pas trouvable dans le fichier %file%',array('%columns%'=>  implode(',', $missedColumns),'%file%'=>$filename));
                //                        $this->saveLog($log,$message,'error');
                //                    }
                //                    return false;
                //                }

                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                global $kernel;
                $kernel->getContainer();
                $m = $kernel->getContainer()->get('doctrine_mongodb.odm.default_connection');
                // select a database
                $db = $m->selectDatabase('satisfactory');
//                print_r($filtreCorrespondance);
//                echo "\r\n----------------------------------------\r\n";
                
                
                $columnsInFiltreCorrespondance = CronRepository::detectBracket($filtreCorrespondance);
//                var_dump($columnsInFiltreCorrespondance);
//                $filtreCorrespondance = CronRepository::replaceBracket($filtreCorrespondance,$fileContent[0],$columnsInFiltreCorrespondance);
//                print_r($filtreCorrespondance);
//                echo "\r\n";
//                return false;

                
                // select a collection (analogous to a relational database's table)
                $collection = $db->selectCollection('TDC_'.$correspondance->getId());
                $fileContentCorrespond = array();
                
                foreach($fileContent as $k => $row) 
                {
                        //if the filter return true, then we apply the correspondences
                        if($this->executeFilterJson($filtre, $row)) 
                        {
                            $fileContentCorrespond[$k] = $row;
                        }
                }
                echo '$fileContentCorrespond = '.count($fileContentCorrespond);
                echo "\r\n";
                if($fileContentCorrespond) {
                    //if the filter do not contain columns, the we have to make the query to mongo only one time
                    if(!$columnsInFiltreCorrespondance) {
                        $document = $collection->findOne($filtreCorrespondance);
                        if($document) {
                            foreach($fileContentCorrespond as $k => $row) 
                            {
                                $fileContent[$k][$columnOutput] = $document[$correspondanceOutput];
                            }
                        }
                    }
                    else {
                        echo "\r\nCASE - il y a des colonnes \r\n";
                        foreach($fileContentCorrespond as $k => $row) 
                        {

                            //if the filter return true, then we apply the correspondences
//                            print_r($filtreCorrespondanceCurrent);
//                            echo "--------------------\n";
                            if($columnsInFiltreCorrespondance) 
                                $filtreCorrespondanceCurrent = CronRepository::replaceBracket($filtreCorrespondance,$row,$columnsInFiltreCorrespondance);
                            $document = $collection->findOne($filtreCorrespondanceCurrent);
                            if($document) {
                                $fileContent[$k][$columnOutput] = $document[$correspondanceOutput];
                            }
                        }
                    }
                }
                /*************************************
                 * No need to the llimit or count variables because 
                 * $collection->find does not charge ALL documents 
                 * it only does this on each iteration with getNext()
                 * and destroy automatically every object so the memory 
                 * will not be full
                 **************************************/
//                while ( $cursorCorrespondance->hasNext() )
//                {
//                    $document = $cursorCorrespondance->getNext();
//                    foreach ($fileContentCorrespond as $k=>$row) {
//                        // directly put the value to the $fileContent that will be generated as csv
//                        $fileContent[$k][$columnOutput] = $document[$correspondanceOutput];
//                    }
//                    
//                }
                
                
//                echo '-----------------';
//                echo ("_____".count($collection->find($filtreCorrespondance)->skip(5)->limit(10)));
//                echo "\r\n";
//                return false;
                
                
                
                
                
                
                
                
//                $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
//                echo 'CASE 1 - FIND COUNT'."\r\n";
//
//                $count = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findCountByCorrespondence($correspondance->getId());
//
//                echo 'CASE 1 - COUNT DONE'."\r\n";     
//                echo 'CASE 1 - COUNT CORRES : '."$count\r\n";
//                echo 'CASE 1 - COUNT LINES  : '.count($fileContent)."\r\n";
//
//                // Precompute if preg_match needed !
//
//                // Precompute correspondances
//                $offset         = 0;
//                $threshold      = 1000;
//                $fullThreshold  = 2000;
//                $done           = array();
//                $repo           = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence');
//                gc_enable();
//                while( $count >= $offset )
//                {
//                    echo 'CASE 1 - PRECOMPUTING'."\r\n";
//                    $startTime  = microtime(true);
//                    $tempOffset = 0;
//                    $computed   = array();
//
//                    while( $fullThreshold >= $tempOffset && $count >= $offset)
//                    {
//                        $entries = $repo->getPaginatedResults( $threshold, $offset, $correspondance->getId(), false);
//
//                        foreach($entries as $entry)
//                        {
//                            $computed[] = $entry['input'];
//                        }
//
//                        $offset     += $threshold;
//                        $tempOffset += $threshold;
//
//                        echo 'CASE 1 - COMPUTED '.$offset." OF ".$count." (".(round(memory_get_usage(true)/1048576,2))." megabytes".")\r\n";
//                    }                                            
//
//                    echo 'CASE 1 - PRECOMPUTING END - TOOK '.(microtime(true) - $startTime)."\r\n";
//                    $startTime = microtime(true);
//
//                    foreach($fileContent as $k => $row) 
//                    {
//                        // One row = one filtering. If already filtered, skip it.
//                        if( isset($done[$k]) )
//                            continue;
//
//                        //if the filter return true, then we apply the correspondences
//                        if($this->executeFilterJson($filtre, $row)) 
//                        {
//                            //
//                            //$regexTime = 0;
//
//                            //echo 'TESTING COMPUTED'."\r\n";
//                            foreach ($computed as $inputs) 
//                            {
//                                //$regexStart = microtime(true);
//
//                                $valid = $this->executeFilterJson($filtreCorrespondance, $inputs,$row);
//
//                                if ($valid) 
//                                {
//                                    $done[$k] = 1;
//                                    echo 'VALID FOUND.'."\r\n";
//                                    // all input matches the content of that row in the file, then we apply the change in output
//                                    $fileContent[$k][$columnOutput] = $inputs[$correspondanceOutput];
//                                    break;
//
//                                }
//                                //$regexTime += (microtime(true) - $regexStart);
//                            }
//                            //$endTime = microtime(true);
//                            //echo 'TESTING COMPUTED END - TOOK '.($endTime - $startTime)." with ".$regexTime." on REGEX / testing. \r\n"; 
//                            //echo 'TESTING COMPUTED END - TOOK '." with "." on REGEX / testing. \r\n"; 
//                        }
//
//                    }
//
//                    echo 'LINE '.$k." - ".(microtime(true) - $startTime)." DONE\r\n";
//
//                    // Freein' some memory ...
//                    $computed = null;
//                    gc_collect_cycles();
//                }
//
//
//                unset($computed);

                break;

            case 2 : // appliquer une règle d'agrégation textuelle :
                echo 'CASE 2 - JSON DONE'."\r\n";
                $column = $operation->getEnrichColumnNameRuleSource();
                $hasColumn = false;
                foreach ($fileContent[0] as $key =>$val) {
                    if($column == $key) {
                        $hasColumn = true;
                    }
                }
                echo 'CASE 2 - 3'."\r\n";
                if (!$hasColumn) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }

                $rule = $operation->getEnrichRule();
                echo 'CASE 2 - 4'."\r\n";
                preg_match_all("/(?<=\[).+?(?=\])/", $rule, $columns);                
                foreach($fileContent as $k=>$row) {
                    //if the filter return true, then we apply the correspondences
                    if($this->executeFilterJson($filtre, $row)) {
                        $string = $rule;
                        //apply rule  for all rows
                        if($columns[0]) {
                            foreach ($columns[0] as $c) {
                                if(isset($row[$c]))
                                    $string = str_replace('['.$c.']',$row[$c],$string);
                            }
                        }
                        $row[$column] = CronRepository::convertHorodate($string);
                        //                        $row[$column] = $string;

                        $fileContent[$k] = $row;
                    }
                }

                break;

        }
        echo 'ENRICH-5-AFTER'."\r\n";
        //reset the keys of the array
        $fileContent = array_values($fileContent);
        $fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;

        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        echo 'ENRICH-6'."\r\n";
        if($list) {
            foreach ($list as $fields) {
                foreach ($fields as $k => $field) {
                    //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                    $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                }
                if($dealing->getQuotation()=="oui")
                    fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                else  {
                    $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                    $fields.="\n";
                    fputs($fp, $fields);
                }
            }
        }
        //        $content = file_get_contents($f);
        fclose($fp);

        //         $fp = fopen($f, "w");
        //        
        //        # Now UTF-8 - Add byte order mark 
        //        fprintf( $fp, mb_convert_encoding($content, 'UTF-8') );
        echo 'ENRICH-7-AFTER'."\r\n";
        return $fileTmpName;
    }


    /*
    *******************************************************************************
    ********Important : TODO : change this to mongodb request *********************
    *******************************************************************************
    */
    protected function getEnterCorrespendance($correspondenceId)
    {
        //EnterCorrespondence list
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $documents = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findByCorrespondence($correspondenceId);
        return $documents;
        //        For mysql purpose and tests
        //        $em = $this->getContainer()->get('doctrine')->getManager();
        //        return $em->getRepository('SatisfactoryOperationBundle:EnterCorrespondance')->findBy(array('correspondenceId' => $correspondenceId));

    }

    /*
    * check if the given row is applyed to the fiter 
    */
    protected function filterByRowEnrich($row,$operation,$log = null)
    {
        $column = $operation->getEnrichColumnFilter();
        switch($operation->getEnrichValue()) {
            case 1 : // Egal a une valeur
                return ($operation->getEnrichEgal() == $row[$column]);
                break;
            case 2 : // Different d\’une valeur 
                return ($operation->getEnrichDifferent() != $row[$column]);
                break;
            case 3 : // superieur ou egal a une valeur
                return (strnatcmp($row[$column],$operation->getEnrichSuperiorOrEgal()) >= 0);
                break;
            case 4 : //  Inferieur ou egal a une valeur
                return (strnatcmp($row[$column],$operation->getEnrichInferiorOrEgal()) <= 0);
                break;
            case 5 : // Compris entre 2 valeurs
                return ((strnatcmp($row[$column],$operation->getEnrichIncludingMin()) >= 0) && (strnatcmp($row[$column],$operation->getEnrichIncludingMax()) <= 0));
                break;
            case 6 : // valeur vide
                return $row[$column] == "";
                break;
            case 7 : // valeur non vide
                return $row[$column] != "";
                break;
        }
        return false;
    }


    protected function executeOperationArchiver($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }

        $ftp = new SFTP($this->getContainer()->getParameter('cronArchiveHost'),$this->getContainer()->getParameter('cronArchiveLogin'), $this->getContainer()->getParameter('cronArchivePassword'),$this->getContainer()->getParameter('cronArchivePort')); 
        if($this->getContainer()->getParameter('cronArchiveSSL')=="sftp")
            $ftp->ssl=true;
        $conn_ftp = @$ftp->connect();



        if($this->getContainer()->getParameter('cronArchiveSSL')=="sftp") {
            $ftp = @new Net_SFTP($this->getContainer()->getParameter('cronArchiveHost'),$this->getContainer()->getParameter('cronArchivePort'));
            $conn_ftp = @$ftp->login($this->getContainer()->getParameter('cronArchiveLogin'), $this->getContainer()->getParameter('cronArchivePassword'));
        }
        else {
            $ftp = new SFTP($this->getContainer()->getParameter('cronArchiveHost'),$this->getContainer()->getParameter('cronArchiveLogin'), $this->getContainer()->getParameter('cronArchivePassword'),$this->getContainer()->getParameter('cronArchivePort')); 
            $conn_ftp = $ftp->connect();
        }

        if (!$conn_ftp) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Impossible de se connecter au FTP');
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Connexion FTP etablie');
            $this->saveLog($log,$message);
        }

        $localFilename = $tmpData['tmpDir'].$tmpData['tmpOriginalFileName'];        

        if($this->getContainer()->getParameter('cronArchiveSSL')=="sftp") {
            if($ftp->put($this->getContainer()->getParameter('cronArchiveDir').'arch_'.date('YmdHis').'_'.$tmpData['tmpOriginalFileName'], file_get_contents($localFilename))) { 
                //log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('copie de fichier distant  operation #%id% %link%',array('id'=>$operation->getId(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message);
                }
                $this->logIfRejectsExist($localFilename,$dealing,$operation);

                if(CronRepository::deleteFileFromFtp($dealing)) {
                    //log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Fichier original supeerimer OK');
                        $this->saveLog($log,$message);
                    }
                } else {
                    //log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Fichier original supeerimer KO');
                        $this->saveLog($log,$message,'warning');
                    }
                }
                return $tmpData;
            }
        }
        else {

            if(@$ftp->put($localFilename, $this->getContainer()->getParameter('cronArchiveDir').'arch_'.date('YmdHis').'_'.$tmpData['tmpOriginalFileName'])) { 
                //log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('copie de fichier distant  operation #%id% %link%',array('id'=>$operation->getId(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message);
                }
                $this->logIfRejectsExist($localFilename,$dealing,$operation);

                if(CronRepository::deleteFileFromFtp($dealing)) {
                    //log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Fichier original supeerimer OK');
                        $this->saveLog($log,$message);
                    }
                } else {
                    //log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Fichier original supeerimer KO');
                        $this->saveLog($log,$message,'warning');
                    }
                }
                return $tmpData;
            }
        }
        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Erreur : impossible envoyer le fichier destination');
            $this->saveLog($log,$message,'error');
        }
        return $tmpData;



    }


    protected function executeOperationRejeter($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);

        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id%',array('%id%'=>$operation->getid(),'%file%'=>$filename));
            $this->saveLog($log,$message);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $reject = $em->getRepository('SatisfactoryOperationBundle:Reject')->find($operation->getReject());

        $column = $reject->getColumnName();
        if (!array_key_exists ($column, $fileContent[0])) {
            // log here error column not found
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        switch ($reject->getType()) {
            case 'Sollicitation' :
                $arrayOfValues = array();
                foreach ($fileContent as $row) {
                    $arrayOfValues[] = $row[$column];
                }
                $rejectExecution = new RejectExecution();
                $rejectExecution->setColumnContent($column);
                $rejectExecution->setContent($arrayOfValues);
                $rejectExecution->setIdOperation($operation->getId());
                $rejectExecution->setCreatedAt( date('Y-m-d H:i:s'));
                $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
                $dm->persist($rejectExecution);
                $dm->flush();
                $rejectExecution->getContent();
                $nbjours = NULL;
                switch ($reject->getProcessBy()) {
                    case 1: // par jour
                        $nbjours = $reject->getProcessDay();
                        break;
                    case 2: // par semaine
                        $nbjours = intval($reject->getProcessWeek())*7;
                        break;
                    case 3: // par mois
                        $nbjours = intval($reject->getProcessMonth())*30;
                        break;
                }
                $rejectExecutions = $dm->getRepository('SatisfactoryCronBundle:RejectExecution')->findByDate($nbjours,$operation->getId());
                $rejectExecutions->toArray();
                $allColumnValues = array();
                if($rejectExecutions) {
                    foreach ( $rejectExecutions as $re) {
                        $allColumnValues = array_merge($allColumnValues,$re['content']);
                    }
                }
                $allColumnValues =$ValuesAfterLimit= array_count_values($allColumnValues);
                if($allColumnValues) {
                    foreach ($allColumnValues as $value=>$count) {
                        if(intval($count) > $reject->getMaxProcess()) {
                            unset($ValuesAfterLimit[$value]);
                        }
                    }
                }
                //                echo '<pre>';
                //                print_r($ValuesAfterLimit);
                //                echo '</pre>';
                //                exit('----');
                if($ValuesAfterLimit) {
                    $ValuesAfterLimit = array_keys($ValuesAfterLimit);
                    foreach ($fileContent as $k=>$row) {
                        if(!in_array($row[$column], $ValuesAfterLimit)) {
                            unset($fileContent[$k]);
                        }
                    }
                }
                else{
                    //all values surpass the limit, then we got an empty array. So we have to make an empty array so the generated csv file will be generated correctly so it contain only header (without values)
                    $fileContent = array_slice($fileContent, -1);
                    foreach ($fileContent[0] as $k => $v)
                        $fileContent[0][$k] = '';
                }
                //reset the keys of the array
                $fileContent = array_values($fileContent);
                if($this->getDebugMode()) {
                    $message = $this->get('translator')->trans('notification operation rejet');
                    $this->saveLog($log,$message);
                }
                break;

            case 'Blackliste' :
                $fileOfValues = $reject->getWebPathForCron().$reject->getPath();
                $blacklistedValues = CronRepository::parseFile($fileOfValues);

                if (!$blacklistedValues) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : probleme dans le fichier du rejet #%id%  %file% %link%',array('%id%'=>$reject->getid(),'%column%'=>$column,'%file%'=>$fileOfValues,'%link%'=> $this->getContainer()->get('router')->generate('operation_reject_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                if($blacklistedValues) {
                    foreach ($fileContent as $k=>$row) {
                        if(in_array($row[$column], $blacklistedValues)) {
                            unset($fileContent[$k]);
                        }
                    }
                }
                else{
                    //all values surpass the limit, then we got an empty array. So we have to make an empty array so the generated csv file will be generated correctly so it contain only header (without values)
                    $fileContent = array_slice($fileContent, -1);
                    foreach ($fileContent[0] as $k => $v)
                        $fileContent[0][$k] = '';
                }
                //reset the keys of the array
                $fileContent = array_values($fileContent);

                break;
            case 'Quota' :

                break;

        }
        //reset the keys of the array
        $fileContent = array_values($fileContent);
        $fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;
        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        if($list) {
            foreach ($list as $fields) {
                foreach ($fields as $k => $field) {
                    //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                    $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                }
                if($dealing->getQuotation()=="oui")
                    fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                else  {
                    $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                    $fields.="\n";
                    fputs($fp, $fields);
                }
            }
        }
        fclose($fp);
        return $fileTmpName;

    }



    protected function executeOperationFiltrageClient($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);

        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id%',array('%id%'=>$operation->getid(),'%file%'=>$filename));
            $this->saveLog($log,$message);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $FilterClient = $em->getRepository('SatisfactoryOperationBundle:Filtering')->find($operation->getFiltering());

        $column = $FilterClient->getColumnName();
        if (!array_key_exists ($column, $fileContent[0])) {
            // log here error column not found
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : colonne "%column%" non trouvable pour l\'operation #%id% %file% %link%',array('%id%'=>$operation->getid(),'%column%'=>$column,'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        if($FilterClient->getIsActive() == 1 ) {
            if(($FilterClient->getDateStart() == NULL)  && ($FilterClient->getDateEnd() == NULL)) {
                switch ($FilterClient->getType()) {
                    case 1 :
                        foreach ($fileContent as $k=>$row) {
                            if(($row[$column] != $FilterClient->getValue())) {
                                unset($fileContent[$k]);
                            }
                        }

                        break;
                    case 2 :
                        foreach ($fileContent as $k=>$row) {
                            if(($row[$column] == $FilterClient->getValue())) {
                                unset($fileContent[$k]);
                            }
                        }

                        break;

                }
            }
            elseif (($FilterClient->getDateStart()->format("Y-m-d H:i:s") <= date("Y-m-d H:i:s"))  && ($FilterClient->getDateEnd()->format("Y-m-d H:i:s") >= date("Y-m-d H:i:s"))) {

                switch ($FilterClient->getType()) {
                    case 1 :
                        foreach ($fileContent as $k=>$row) {
                            if(($row[$column] != $FilterClient->getValue())) {
                                unset($fileContent[$k]);
                            }
                        }

                        break;
                    case 2 :
                        foreach ($fileContent as $k=>$row) {
                            if(($row[$column] == $FilterClient->getValue())) {
                                unset($fileContent[$k]);
                            }
                        }

                        break;

                }
            }
        }
        //reset the keys of the array
        $fileContent = array_values($fileContent);
        $fileContent = CronRepository::genrateCsvFormat($fileContent);
        $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
        $f = $tmpData['tmpDir'].$fileTmpName;
        $list = $fileContent;
        $fp = fopen($f, 'w');
        if(!$fp) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        if($list) {
            foreach ($list as $fields) {
                foreach ($fields as $k => $field) {
                    //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                    $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                }
                if($dealing->getQuotation()=="oui")
                    fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                else  {
                    $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                    $fields.="\n";
                    fputs($fp, $fields);
                }
            }
        }
        fclose($fp);
        return $fileTmpName;

    }



    protected function executeOperationTableCorrespondance($dealing,$operation,$tmpData,$operationNum,$log = null)
    {
        if(!$operation || !$dealing || !$tmpData) {
            return false;
        }
        $filename = $tmpData['tmpDir'].$tmpData['tmpFileName'];
        if(!file_exists($tmpData['tmpDir'].$tmpData['tmpFileName'])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Fichier a traiter non trouve pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        $fileContent = CronRepository::parseCSVFile($filename, $dealing);

        if(!$fileContent) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : probleme parse du fichier "%file%" pour l\'operation %id% %link%',array('%id%'=>$operation->getid(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        // log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Parse du fichier "%file%" termine pour l\'operation %id%',array('%id%'=>$operation->getid(),'%file%'=>$filename));
            $this->saveLog($log,$message);
        }

        ///////////////////////////////////////////////////////////
        $correspondance = $operation->getCorrespondance();
        $columns = $correspondance->getColumns();
        if(!$columns) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : correspondance ne contient pas des colonnes %correspondance% %link%',array('%correspondance%'=>$correspondance->getName(),'%file%'=>$filename,'%link%'=> $this->getContainer()->get('router')->generate('operation_correspondance_edit', array('id' => $correspondance->getId()))));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }


        if(count($columns) != count($fileContent[0])) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : La correspondance ne contient pas le meme nombre de colonne que le fichier source du traitement');
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        // we must test if all columns in the correspondance already exists in the file. If not then generate a log error
        $missedColumns = array();
        foreach ($columns as $c) {
            if(!array_key_exists($c,$fileContent[0])) {
                $missedColumns[] = $c;
            }
        }
        echo "SEEMS OK.\r\n";
        if($missedColumns) {
            // log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur : Les colonnes : %columns% pas trouvable dans le fichier %file%',array('%columns%'=>  implode(',', $missedColumns),'%file%'=>$filename));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        echo "SEEMS OK2.\r\n";
        $header= array() ;
        foreach ($fileContent[0] as $k=>$value) {
            $header[$k] = "";
        }
        // empty the original file so we put new values
        

        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $m = $this->getContainer()->get('doctrine_mongodb.odm.default_connection');
        $m = $this->getContainer()->get('doctrine_mongodb.odm.default_connection');
        // select a database
        $db = $m->selectDatabase('satisfactory');

        // select a collection (analogous to a relational database's table)
        $collection = $db->selectCollection('TDC_'.$correspondance->getId());
        $collection->drop();

        echo "PADDING \r\n";
        $count  = 2;
        $date   = new \DateTime();
        $author = "Anaïs Martin";
        $cid    = $correspondance->getId();
        $collection = $db->createCollection('TDC_'.$cid);
        $headerCorrespondance = $correspondance->getColumns();
        foreach($fileContent as $row)
        {
            if($count == 1) {
                $headerCorrespondance = $row;
                
                foreach($headerCorrespondance as $key => $head)
                {
                    $headerCorrespondance[$key] = (preg_match('!!u', $head)) ? $head : utf8_encode($head);
                }
                
                
                $correspondance->setColumns($headerCorrespondance);
                $em->persist($correspondance);
                $em->flush();
                echo "COLUMN SET\r\n";
            }
            if(count($row) == ($correspondance->getColumnnumber()) && $count > 1) {
                
                #####################################################################
                ##This eliminate every wrong row from the csv, TO VERIFY THIS LATER##
                ###It will make sure that will save only rows with only X values####
                #####################################################################
                var_dump($row);
                
                $document = array();
                
                foreach($row as $key => $value)
                {
                    $document[$key] = (preg_match('!!u', $value)) ? $value : utf8_encode($value);
                }
                
//                $queryBuilder = $dm->createQueryBuilder('SatisfactoryOperationBundle:EnterCorrespondence');
//                $queryBuilder->insert('SatisfactoryOperationBundle:EnterCorrespondence');
                // connect
                
                

                // select a collection (analogous to a relational database's table)
                
                
                echo "ADDING COLUMN\r\n";
                $document['correspondenceId'] = $cid;
                $document['createdAt'] = $date;
                $document['createdBy'] = $author;
                
                $collection->insert($document);
//                for($i=0;$i < count($row);$i++) 
//                    $queryBuilder->setValue($headerCorrespondance[$i], $row[$i]);
//                $query->getQuery()->execute();
//                $document = new EnterCorrespondence();
//                
//                $document->setCorrespondenceId($cid);
//                $document->setInput($input);
//                $document->setCreatedAt($date);
//                $document->setCreatedBy($author);
//                
//                //Persist and flush mongoDb
//                $dm->persist($document);
                
                
            }
            $count++;
        }
        $correspondance->setUpdatedAt($date);
        $em->persist($correspondance);
        $em->flush();
        $dm->flush();
        
        return $filename;
        /*$count = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findCountByCorrespondence($correspondance->getId());
        echo "SEEMS OK3.\r\n";
        if($count > 100) {
        
            $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
            $f = $tmpData['tmpDir'].$fileTmpName;
            $offset = 0;
            echo "SEEMS OK4.\r\n";
            do {
                $fileContent = array();
                $enters = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->getPaginatedResults(100,$offset,$correspondance->getId());
                $enters->toArray();
                
                foreach ($enters as $enter) 
                {
                    $fileContent[] = $enter->getInput();
                }
                ///////////////////////////////////////////////////////////

                //reset the keys of the array
                $fileContent = array_values($fileContent);

                //see if we put the header or not. Test is done by testing the offset value (if it's 0 then we are in the beginning of the file then we have to put the header, else we don't)
                $putHeader = $offset==0 ? true : false;

                $fileContent = CronRepository::genrateCsvFormat($fileContent,$putHeader);
                $list = $fileContent;
                $fp = fopen($f, 'a');
                if(!$fp) {
                    // log here
                    if($this->getDebugMode()) {
                        $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                        $this->saveLog($log,$message,'error');
                    }
                    return false;
                }
                if($list) {
                    echo "SEEMS OK - LIST.\r\n";
                    foreach ($list as $fields) {
                        foreach ($fields as $k => $field) {
                            //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                            $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                        }
                        if($dealing->getQuotation()=="oui")
                            fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                        else  {
                            $fields = implode (CronRepository::GetCsvSeparatorFromDealing($dealing),$fields);
                            $fields.="\n";
                            fputs($fp, $fields);
                        }
                    }
                }
                $offset+=100;

            }
            while ($count >= $offset);
            fclose($fp);
        }
        else {
       
            $enters = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findByCorrespondence($correspondance->getId());
            foreach ($enters as $enter) {
                $fileContent[] = $enter->getInput();
            }
            ///////////////////////////////////////////////////////////
            echo "SEEMS OK8.\r\n";
            print_r($fileContent);
            //reset the keys of the array
            $fileContent = array_values($fileContent);
            $fileContent = CronRepository::genrateCsvFormat($fileContent);
            $fileTmpName = date('Y.m.d.H.i.s',time()).'-'.$operationNum."-OP".$operation->getId().".csv";
            $f = $tmpData['tmpDir'].$fileTmpName;
            $list = $fileContent;
            $fp = fopen($f, 'w');
            if(!$fp) {
                // log here
                if($this->getDebugMode()) {
                    $message = $this->getContainer()->get('translator')->trans('Erreur : impossible de generer le fichier resultat pour l\'operation #%id% %dir% %link%',array('%id%'=>$operation->getid(),'%dir%'=>$tmpData['tmpDir'],'%link%'=> $this->getContainer()->get('router')->generate('operation_operation_edit', array('id' => $operation->getId()))));
                    $this->saveLog($log,$message,'error');
                }
                return false;
            }
            if($list) {
                echo "SEEMS OK9.\r\n";
                foreach ($list as $fields) {
                    foreach ($fields as $k => $field) 
                    {
                        //$fields[$k] =  mb_convert_encoding($field, 'UTF-8');
                        $fields[$k] = (preg_match('!!u', $field)) ? $field : utf8_encode($field); 
                    }
                    echo "SEEMS OK9.\r\n";    
                    if($dealing->getQuotation()=="oui")
                        fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing),'"');
                    else  {
                        fputcsv($fp, $fields, CronRepository::GetCsvSeparatorFromDealing($dealing));
                    }
                }
            }
            fclose($fp);
            return $fileTmpName;

        } */
    }


    private function logIfRejectsExist($localFilename,$dealing,$operation)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $rejectOperations = array();
        $orderedOperations = $em->getRepository('SatisfactoryOperationBundle:Operation')->getAcitfOrderOperations($dealing);
        if($orderedOperations) {
            foreach ($orderedOperations as $operation) {
                if($operation->getType()=="Rejected")
                    $rejectOperations[] = $operation;
            }
            if($rejectOperations) {
                $fileContent = CronRepository::parseCSVFile($localFilename, $dealing);
                if(!$fileContent) {
                    return false;
                }
                $logArray = array(); // array that will contain all published values of the
                foreach ($rejectOperations as $rejectOperation) {
                    $reject = $em->getRepository('SatisfactoryOperationBundle:Reject')->find($rejectOperation->getReject());
                    if($reject->getType() == "Sollicitation") {
                        $column = $reject->getColumnName();
                        foreach ($fileContent as $row) {
                            $logArray[$column][] = $row[$column];
                        }
                    }
                }
                if($logArray) {
                    // save log
                    $rejectLog = new \Satisfactory\CronBundle\Document\RejectLog();
                    $rejectLog->setContent($logArray);
                    $rejectLog->setIdDealing($dealing->getId());
                    $rejectLog->setCreatedAt( date('Y-m-d H:i:s'));
                    $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
                    $dm->persist($rejectLog);
                    $dm->flush();
                }
            }
        }
    }




    /*************************Cron functions to deal with ftp, csv etc*******************************************/

    /**
    * retreave file from FTP and put it in the temporary folder
    *  
    * @param entity $dealing 
    * @return string name of the tmp file. False if there are errors.
    */
    public  function cloneFileFromFtp($dealing, $destinationDir,$dateInitexecution,$log = null,$tmpName = NULL)
    {
        if($dealing->getProtocol()=="sftp") {
            $ftp = @new Net_SFTP($dealing->getHost(),$dealing->getPort());
            $conn_ftp = @$ftp->login($dealing->getLogin(), $dealing->getPassword());
        }
        else {
            $ftp = new SFTP($dealing->getHost(),$dealing->getLogin() , $dealing->getPassword(),$dealing->getPort()); 
            $conn_ftp = $ftp->connect();
        }
        if (!$conn_ftp) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Impossible de se connecter au FTP');
                $this->saveLog($log,$message,'error');
            }
            return false;
        }

        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Connexion FTP etablie');
            $this->saveLog($log,$message);
        }
        $remoteFilename = CronRepository::convertHorodate($dealing->getFileName());

        $tmpFileName = CronRepository::generateTmpFileNameOriginal($remoteFilename,$dealing->getId());

        if($tmpName)
            $tmpFileName = $tmpName;
        $currentDirForDealing = CronRepository::createDirStructure($dealing, $destinationDir, $dateInitexecution);
        if(!$currentDirForDealing) {
            //log here
            if($this->getDebugMode()) {
                $message = $this->getContainer()->get('translator')->trans('Erreur creation dossier principal du log %path%',array('%path%'=>'/web/'.$this->getContainer()->getParameter('cronTmpDir')));
                $this->saveLog($log,$message,'error');
            }
            return false;
        }
        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Creation dossier principale de log');
            $this->saveLog($log,$message);
        }
        $ftpDirectory = $dealing->getDirectory();
        if(substr($ftpDirectory, -1) !='/')
            $ftpDirectory.='/';


        if(@$ftp->get($ftpDirectory.$remoteFilename, $currentDirForDealing.$tmpFileName)) { 
            //var_dump(mb_detect_encoding(file_get_contents($currentDirForDealing.$tmpFileName), mb_detect_order(), TRUE));
            return array(
                'tmpDir'=>$currentDirForDealing,
                'tmpFileName'=>$tmpFileName,
                'tmpOriginalFileName'=>$tmpFileName,
            );
        }

        //log here
        if($this->getDebugMode()) {
            $message = $this->getContainer()->get('translator')->trans('Erreur : fichier cible non trouve sur le serveur');
            $this->saveLog($log,$message,'error');
        }
        return false;
    }





    /*************************Begin Mail Functions*******************************************/
    public function sendMailNotifications($log) 
    { 
        //$initTimeCron = strtotime($initTimeCron);
        //$initTimeCron = new \DateTime($initTimeCron);
        if(!$log)
            return false;
        $em = $this->getContainer()->get('doctrine')->getManager();
        $mailNotifications= $em->getRepository('SatisfactoryOperationBundle:Notification')->findBy(array('dealing' => $log->getDealing()));
        if(!$mailNotifications) {
            return false;
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        //$log = $em->getRepository('SatisfactoryCronBundle:Cronexecution')->findOneBy(array('dealing'=>$dealing->getId(),'beginAt'=>$initTimeCron));
        //$log = $em->getRepository('SatisfactoryCronBundle:Cronexecution')->findOneBy(array('dealing'=>48,'beginAt'=>'2016-03-31 11:59:19'));
        //if(!$log)
        //    return false;
        $logContent = $log->getLog();
        foreach ($mailNotifications as $mail) {
            $emailText ="Résultat de l'exécution du Cron du cron : \n\r\n\r";
            $sendType = $mail->getType();
            if($sendType) {
                if(count($sendType) == 2) {
                    // send all log because the reciever want to have both success and error log
                    foreach ($logContent as $content) {
                        $emailText.=$content['date']." - ".$content['message']." \n\r";
                    }
                    $message = @\Swift_Message::newInstance()
                    ->setSubject("Satisfactory - Résultat d'exécution du Cron")
                    ->setFrom(array('noreply@satisfactory.com' => 'Satisfactory - Notifications Cron'))
                    ->setTo($mail->getEmail())
                    ->setBody($emailText);
                    $this->getContainer()->get('mailer')->send($message);

                }
                else {
                    if($sendType[0]) {
                        if($sendType[0] == "error") {
                            // send only log messages that contain the "error" status
                            foreach ($logContent as $content) {
                                if($content['status'] == "error")
                                    $emailText.=$content['date']." - ".$content['message']." \n\r";
                            }
                            $message = @\Swift_Message::newInstance()
                            ->setSubject("Satisfactory - Résultat d'exécution du Cron")
                            ->setFrom(array('noreply@satisfactory.com' => 'Satisfactory - Notifications Cron'))
                            ->setTo($mail->getEmail())
                            ->setBody($emailText);
                            $this->getContainer()->get('mailer')->send($message);
                        }
                        else {
                            $errors = false;
                            foreach ($logContent as $content) {
                                if($content['status'] == "error")
                                    $errors = true;
                            }
                            if(!$errors) {
                                // send all log because there is no errors
                                foreach ($logContent as $content) {
                                    $emailText.=$content['date']." - ".$content['message']." \n\r";
                                }
                                $message = @\Swift_Message::newInstance()
                                ->setSubject("Satisfactory - Résultat d'exécution du Cron")
                                ->setFrom(array('noreply@satisfactory.com' => 'Satisfactory - Notifications Cron'))
                                ->setTo($mail->getEmail())
                                ->setBody($emailText);
                                $this->getContainer()->get('mailer')->send($message);
                            }
                        }
                    }

                }
            }
        }

    }
    /*************************END Mail Functions*******************************************/





    /*************************Begin Log Functions*******************************************/

    /*
    * TODO : test if mode debug is active or not.
    */
    public function getDebugMode() 
    {
        return true;
    }

    /*
    * Init the log and save the row in the satisfactory_cronexecution table
    * Each row contain the date of the beginning of the execution of the Dealing, date of the end of execution, and the log saved as array in the field "log"
    */
    public function logBeginDealing($dealing, $message, $status = 'success',$dateBegin)
    {
        $log = array();
        $log[] = array(
            'date' => $dateBegin,
            'status'=>$status,
            'message'=>$message

        );
        $em = $this->getContainer()->get('doctrine')->getManager();
        $cronexecution = new Cronexecution();
        /*$date = new \DateTime();

        $cronexecution->setBeginAt($date);
        //Set the execute date of dealing entity
        $dealing->setExecutedAt($date);

        $cronexecution->setRunning(true);
        $cronexecution->setLog($log);
        $cronexecution->setDealing($dealing);
        $em->persist($cronexecution);
        $em->flush();*/
        return $cronexecution;

    }

    /*
    * Init the log and save the row in the satisfactory_cronexecution table
    * Each row contain the date of the beginning of the execution of the Dealing, date of the end of execution, and the log saved as array in the field "log"
    */
    public function logEndDealing($log,$message,$dealing,$status = 'success')
    {
        if(!$log)
            return false;
        $em = $this->getContainer()->get('doctrine')->getManager();

        $logArray = $log->getLog();
        if($logArray) {
            foreach($logArray as $row) {
                if($row["status"]=='error')
                    $status = 'error';
            }
        }
        $logArray[] = array(
            'date' => date('Y-m-d H:i:s',  time()),
            'status'=>$status,
            'message'=>$message

        );

        // save the last execution status for dealing
        if($status == "success")
        {
            $dealing->setExecutedStatus(1);
        }                                  

        $log->setLog($logArray);
        $log->setEndAt($date = new \DateTime());
        $log->setRunning(false);
        //$em->persist($log);
        //$em->flush();

    }

    /*
    * save the log messages into the given log 
    */
    public function saveLog($log,$message,$status = 'success')
    {
        if(!$log)
            return false;
        $em = $this->getContainer()->get('doctrine')->getManager();
        $logArray = $log->getLog();

        $logArray[] = array(
            'date' => date('Y-m-d H:i:s',  time()),
            'status'=>$status,
            'message'=>$message

        );
        $log->setLog($logArray);
        //$em->persist($log);
        //$em->flush();



        /*echo "<pre>";
        print_r( $log->getLog());
        echo "</pre>";*/
        //exit('-------');
    }

    protected function saveEnters($correspondance, $filename)
    {
        if(!$correspondance) {
            return false;
        }

        if(!file_exists($filename)) {
            return false;
        }
        $handle = fopen($filename,"r"); 
        $count = 1;
        $headerCorrespondance = $output = $output = array();
        
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $em = $this->getContainer()->get('doctrine')->getManager();

        $date = new \DateTime();
        $corresId = $correspondance->getId();
        while (($row = fgetcsv($handle,0,";")) !== FALSE)
        {
            if($count == 1) {
                $headerCorrespondance = $row;
                $correspondance->setColumns($headerCorrespondance);
                $em->persist($correspondance);
                $em->flush();
            }
            if(count($row) == ($correspondance->getColumnnumber()) && $count > 1) {
                #####################################################################
                ##This eliminate every wrong row from the csv, TO VERIFY THIS LATER##
                ###It will make sure that will save only rows with only X values####
                #####################################################################
                for($i=0;$i<count($row);$i++) {
                    $input[$headerCorrespondance[$i]] = $row[$i];
                }
                $document = new EnterCorrespondence();

                $document->setCorrespondenceId($corresId);
                $document->setInput($input);
                $document->setCreatedAt($date);
                $document->setCreatedBy("Anaïs MARTIN");
                
                //Persist and flush mongoDb
                $dm->persist($document);
                

            }
            $count++;
            echo 'IMPORTED '.$count."\r\n";
        }
        $dm->flush();
    }

}
