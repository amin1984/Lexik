<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Satisfactory\OperationBundle\Entity\Dealing;
use Satisfactory\OperationBundle\Form\DealingType;
use Satisfactory\OperationBundle\Form\DealingEditType;
use Satisfactory\OperationBundle\Entity\Notification;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Satisfactory\CronBundle\Repository\CronRepository;
use Satisfactory\CronBundle\Repository\MimeMailParser;


/**
 * Controller Dealing Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class DealingController extends Controller
{
    /**
     * Lists all Dealing entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        
        return $this->render('SatisfactoryOperationBundle:Dealing:index.html.twig');
    }
    
    /**
     * List of all Dealing entities by order.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxPaginatorOrderListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $countDealings = $em->getRepository('SatisfactoryOperationBundle:Dealing')->countQuery();
        
        $order = $request->get('order') ? $request->get('order') : 'executedAt';
        
        $numPage = $request->get('page') ? $request->get('page') : 1;
        
        $orderType = $request->get('orderType') ? $request->get('orderType') : 'DESC';
        
        $input = $request->get('input') ? $request->get('input') : null;
        
        $client = $request->get('client') ? $request->get('client') : null;
        
        $dealings = $em->getRepository('SatisfactoryOperationBundle:Dealing')->paginatorQuery($limit=10, $offset=(($numPage-1)*$limit), $input, $client, $order, $orderType);
        
        $dealingsLimitOff = $em->getRepository('SatisfactoryOperationBundle:Dealing')->paginatorQueryLimitOff( (($numPage-1)*$limit), $input, $client, $order, $orderType);
        
        $countDealings = count($dealingsLimitOff);
        
        $clients = $em->getRepository('SatisfactoryUserBundle:User')->findByRole('ROLE_CLIENT');
        
        // Clients
        $arrayClients = array();
        $arrayClients[0]['id'] =  'all';
        $arrayClients[0]['name'] =  'Tous les clients';
        foreach ($clients as $key=>$value){
            $arrayClients[$key+1]['id'] =  $value->getId();
            $arrayClients[$key+1]['name'] =  $value->getUsername();
        }
        
        // Dealings list
        $array = array();
        foreach ($dealings as $key=>$value){
            $array[$key]['id'] =  $value->getId();
            $array[$key]['name'] =  $value->getName();
            $array[$key]['client'] =  $value->getClient()->getUsername();
            
            // Operations list
            $arrayOps = array();
            foreach ($value->getOperations() as $k=>$op){ 
              $arrayOps[$k]['id'] = $op->getId();
              $arrayOps[$k]['type'] = $op->getType();
              $arrayOps[$k]['orderItem'] = $op->getOrderItem();
              $arrayOps[$k]['pathOperation'] =  $this->generateUrl('operation_operation_edit',array('id' => $op->getId()));
            }
            $array[$key]['operations'] = $arrayOps;
            
            $array[$key]['creatorName'] =  $value->getCreatorName();
            $date = $value->getUpdatedAt();
            // updatedAt
            if ($value->getUpdatedAt())
                $array[$key]['updatedAt'] = $value->getUpdatedAt()->format('d-m-Y H:i:s');
            else
                $array[$key]['updatedAt'] = null;
            // executedAt
            if ($value->getExecutedAt())
                $array[$key]['executedAt'] = $value->getExecutedAt()->format('d-m-Y H:i:s');
            else
                $array[$key]['executedAt'] = null;
            // Future executed
            $array[$key]['lastExecution'] = $this->getFutureDateByDealing($value);
            $array[$key]['isActive'] =  $value->getIsActive();
            $array[$key]['executedStatus'] =  $value->getExecutedStatus();
            $array[$key]['pathActivate'] =  $this->generateUrl('operation_dealing_activate',array('id' => $value->getId()));
            $array[$key]['pathEdit'] =  $this->generateUrl('operation_dealing_edit',array('id' => $value->getId()));
            $array[$key]['pathDelete'] =  $this->generateUrl('operation_dealing_delete',array('id' => $value->getId()));
            $array[$key]['pathDuplicate'] =  $this->generateUrl('operation_dealing_duplicate',array('id' => $value->getId()));
            $array[$key]['pathExecute'] =  $this->generateUrl('satisfactory_cron_manualexecute',array('id' => $value->getId()));
        }
        
        // Url for page
        $urlPage =  $this->generateUrl('operation_dealing_order_ajax');
        
        return new Response(json_encode(array(
                    'dealings' => $array,
                    'pages' => ceil($countDealings / $limit) ,
                    'currentPage' => $request->get('page')>=1 ? (($request->get('page')-1)*$limit)+1 : 1,
                    'limit' => $limit,
                    'clients' => $arrayClients,
                    'offset' => $offset,
                )
        ));
    }

    /**
     * Creates a new Dealing entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $dealing = new Dealing();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\DealingType', $dealing);
        $form->handleRequest($request);
        
        if($dealing->getSepa())
            $form->getData()->setOther($dealing->getSepa());
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            //save operation data
            $date = new \DateTime();
            $dealing->setCreatedAt($date);
            $dealing->setCreatedBy($this->getUser());
            $dealing->setCreatorName($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealing);
            //save dealing object in notifications 
            if ($dealing->getNotifications()) {
                $notifications = $dealing->getNotifications();
                foreach ($notifications as $notification) {
                    //Validation notification form
                    if (!$notification->getEmail() || !$notification->getType()) {
                        $notificationError = "Vous devez remplir le champ email et choisir au moins un type de notification  !";
                        return $this->render('SatisfactoryOperationBundle:Dealing:new.html.twig', array(
                                    'dealing' => $dealing,
                                    'notificationError' => $notificationError,
                                    'form' => $form->createView(),
                        ));
                    }
                    $notification->setDealing($dealing);
                }
            }

            $em->flush();

            return $this->redirectToRoute('operation_operation_new', array('id' => $dealing->getId()));
        }
        
        return $this->render('SatisfactoryOperationBundle:Dealing:new.html.twig', array(
            'dealing' => $dealing,
            'notificationError' => '',
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Dealing entity.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $dealing
     */
    public function editAction(Request $request, Dealing $dealing)
    {
        // set session to null
        $session = $request->getSession();
        $session->set('correspondanceId', null);
        
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('Satisfactory\OperationBundle\Form\DealingEditType', $dealing);
        $editForm->handleRequest($request);
        //List operations in order
        $orderOperations = $em->getRepository('SatisfactoryOperationBundle:Operation')->getOrderOperations($dealing);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $date = new \DateTime();
            $dealing->setUpdatedAt($date);
            $dealing->setUpdatedBy($this->getUser());
            
            $em->persist($dealing);
            //save dealing object in notifications 
            if ($dealing->getNotifications()) {
                $notifications = $dealing->getNotifications();
                foreach ($notifications as $notification) {
                    //Validation notification form
                    if (!$notification->getEmail() || !$notification->getType()) {
                        $notificationError = "Vous devez remplir le champ email et choisir au moins un type de notification  !";
                        return $this->render('SatisfactoryOperationBundle:Dealing:edit.html.twig', array(
                                    'dealing' => $dealing,
                                    'notificationError' => $notificationError,
                                    'form' => $editForm->createView(),
                                    'operations' => $dealing->getOperations(),
                                    'orderOperations' => $orderOperations
                        ));
                    }
                    $notification->setDealing($dealing);
                }
            }
            
            $em->flush();

            $this->get('session')->getFlashBag()->add('cron_manual_execute_success', 'Opération sauvegardée.');
        
            return $this->redirectToRoute('operation_dealing_index');
        }

        return $this->render('SatisfactoryOperationBundle:Dealing:edit.html.twig', array(
            'dealing' => $dealing,
            'notifications' => $dealing->getNotifications(),
            'notificationError' => '',
            'form' => $editForm->createView(),
            'orderOperations' => $orderOperations
        ));
    }

    /**
     * Deletes a Dealing entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $dealing
     */
    public function deleteAction(Request $request, Dealing $dealing)
    {
        if (!$dealing)
        {
            throw $this->createNotFoundException('Unable to find dealing entity.');
        } 
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            // delete operations first
            $operations = $em->getRepository('SatisfactoryOperationBundle:Operation')->getOrderOperations($dealing);
            if($operations) {
                foreach ($operations as $operation) {
                    $em->remove($operation);
                }
            }
            $em->remove($dealing);
            $em->flush();
        }

        return $this->redirectToRoute('operation_dealing_index');
    }
    
    /**
     * Activate dealing.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $dealing
     */
    public function activateAction(Request $request,  Dealing $dealing)
    {
        if (!$dealing)
        {
            throw $this->createNotFoundException('Unable to find dealing entity.');
        } 
        
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $dealing->setIsActive(!$dealing->getIsActive());
            $em->flush();
        }

        return $this->redirectToRoute('operation_dealing_index');
    }
    
    /**
     * Duplicate dealing.
     *
     * @param Dealing entity $dealing
     */
    public function duplicateAction(Dealing $dealing)
    {
        if (!$dealing)
        {
            throw $this->createNotFoundException('Unable to find dealing entity.');
        } 
        //Clone Dealing entity
        $em = $this->getDoctrine()->getManager();
        $copyDealing = clone $dealing;
        $em->persist($copyDealing);
        $em->flush();
        //save operation data
        $date = new \DateTime();
        $copyDealing->setName($dealing->getName()." - copie ".date('Y/m/d H:i:s'));
        $copyDealing->setCreatedAt($date);
        $copyDealing->setExecutedAt(NULL);
        $copyDealing->setCreatedBy($this->getUser());
        $em->flush();
        //Clone Opertaion entity
        foreach($copyDealing->getOperations() as $operation)
        {
            $copyOperation = clone $operation;
            $em->persist($copyOperation);
            $em->flush();
            $copyOperation->setDealing($copyDealing);
            $em->flush();
        }
        //Clone Notification entity
        foreach($copyDealing->getNotifications() as $notification)
        {
            $copyNotification = clone $notification;
            $em->persist($copyNotification);
            $em->flush();
            $copyNotification->setDealing($copyDealing);
            $em->flush();
        }
        
        return $this->redirectToRoute('operation_dealing_index');
    }
    
    /**
     * verify the csv file and the ftp connection using ajax.
     *
     * @param data 
     */
    public function verifycsvAction()
    {
        $mesage = array();
        $request = $this->get('request');
        parse_str($request->get('data'), $data);
        $fileNameControl = $data['fileNameControl'];
        
        if( isset($data['dealing']))
        {
            $data = $data['dealing'];
        }
        elseif( isset($data['dealing_edit']) )
        {
            $data = $data['dealing_edit'];
        }
        
        if(!empty($fileNameControl)) 
            $data['fileName'] = $fileNameControl;
        $data['fileName'] = CronRepository::convertHorodate($data['fileName']);
                                         
        $ftp = CronRepository::connectFtpSftp($data['protocol'], $data['host'], $data['login'], $data['password'], $data['port']);
        if(!$ftp) {
            $mesage[0]['status'] = 'warning';
            $mesage[0]['message'] = $this->get('translator')->trans('Connection ftp KO');
        }
        else {
            $mesage[0]['status'] = 'success';
            $mesage[0]['message'] = $this->get('translator')->trans('Connection ftp OK');
            //Verify if file extension is CSV
            if(!$data['fileName']) {
                    $mesage[1]['status'] = 'warning';
                    $mesage[1]['message'] = $this->get('translator')->trans('Fichier existe KO');
            }else {
                $file_parts = pathinfo($data['fileName']);
                if(@$file_parts['extension'] != "csv") {
                    $mesage[1]['status'] = 'warning';
                    $mesage[1]['message'] = $this->get('translator')->trans('Fichier csv KO');
                }
                else{
                    $file = CronRepository::fileExistsInFtpSftp($data['protocol'], $ftp, $data['directory'], $data['fileName']);
                    if(!$file) {
                        $mesage[1]['status'] = 'warning';
                        $mesage[1]['message'] = $this->get('translator')->trans('Fichier existe KO');
                    }
                    else {
                        $mesage[1]['status'] = 'success';
                        $mesage[1]['message'] = $this->get('translator')->trans('Fichier existe OK');
                        $separator = $data['sepa'] ? $data['sepa'] : $data['other'];
                        $fileErrors = CronRepository::csvCkeck($file,$separator,array(),$data);
                        if(!$fileErrors) {
                            $mesage[2]['status'] = 'success';
                            $mesage[2]['message'] = $this->get('translator')->trans('Fichier compatible OK');
                        }
                        else {
                            $mesage[2]['status'] = 'warning';
                            $mesage[2]['message'] = $this->get('translator')->trans('Fichier compatible KO %lines%',array('%lines%'=>$fileErrors));
                        }

                    }
                }   
                
            }
        }
        return new Response(json_encode($mesage)); 
    }
    
    
    
    /**
     * get the list of columns manes from the dealing
     *
     */
    public function getFileColumnsAction($id)
    {
        $mesage = array();
        $em = $this->getDoctrine()->getManager();
        $dealing = $em->getRepository('SatisfactoryOperationBundle:Dealing')->find($id);
        
        if(!$dealing) {
            $mesage['status'] = 'warning';
            $mesage['message'] = $this->get('translator')->trans('Traitement KO');
        }
        else {
            $fileName = $dealing->getFilename();
            $fileName = CronRepository::convertHorodate($fileName);

            $ftp = CronRepository::connectFtpSftp($dealing->getProtocol(), $dealing->getHost(), $dealing->getLogin(), $dealing->getPassword(), $dealing->getPort());
            if(!$ftp) {
                $mesage['status'] = 'warning';
                $mesage['message'] = $this->get('translator')->trans('Traitement KO');
            }
            else {
                //Verify if file extension is CSV
                if(!$fileName) {
                    $mesage['status'] = 'warning';
                    $mesage['message'] = $this->get('translator')->trans('Traitement KO');
                }else {
                    $file_parts = pathinfo($fileName);
                    if(@$file_parts['extension'] != "csv") {
                            $mesage['status'] = 'warning';
                            $mesage['message'] = $this->get('translator')->trans('Traitement KO');
                    }
                    else{
                        $file = CronRepository::fileExistsInFtpSftp($dealing->getProtocol(), $ftp, $dealing->getDirectory(), $fileName);
                        if(!$file) {
                            $mesage['status'] = 'warning';
                            $mesage['message'] = $this->get('translator')->trans('Traitement KO');
                        }
                        else {
                            $separator = $dealing->getSepa() ? $dealing->getSepa() : $dealing->getOther();
                            $fileColummns= CronRepository::csvGetColumns($file,$dealing);
                            if($fileColummns) {
                                $mesage['status'] = 'success';
                                $mesage['message'] = $fileColummns;
                            }
                            else {
                                $mesage['status'] = 'warning';
                                $mesage['message'] = $this->get('translator')->trans('Traitement KO');
                            }

                        }
                    }   

                }
            }
        }
        return new Response(json_encode($mesage)); 
    }
    
    protected function getFutureDateByDealing($dealing) {
        
        switch ($dealing->getRecurence()) {
                case 1 :
                    $currentDay = date('N');
                    $daysOfDealing = $dealing->getDays();
                    reset($daysOfDealing);
                    $firstDayForCron = current($daysOfDealing);
                    $days = array(
                        1=>'monday',
                        2=>'tuesday',
                        3=>'wednesday',
                        4=>'thursday',
                        5=>'friday',
                        6=>'saturday',
                        7=>'sunday',
                    );
                    if($currentDay < $firstDayForCron && (date('H:i') < date('H:i',  strtotime($dealing->getTimeDay()->format('Y-m-d H:i:s'))) )) {
                        return date('Y/m/d '.date('H:i',  strtotime($dealing->getTimeDay()->format('Y-m-d H:i:s'))), strtotime('next '.$days[$firstDayForCron]));
                    }
                    else {
                        foreach($days as $k => $day) {
                            if($k < $firstDayForCron)
                                unset($days[$k]);
                        }
                        if(!$days)
                            $futureDay= 1;
                        else {
                            reset($days);
                            $futureDay = current($days);
                        }
                        return date('Y/m/d '.date('H:i',  strtotime($dealing->getTimeDay()->format('Y-m-d H:i:s'))), strtotime('next '.$futureDay));
                    
                    }
                     break;
                case 2 :
                    $currentDay = date('d');
                    if($currentDay < $dealing->getDayOfMonth() && (date('H:i') < date('H:i',  strtotime($dealing->getTimeMonth()->format('Y-m-d H:i:s'))) ) )
                        return date('Y/m/'.$dealing->getDayOfMonth().' '.date('H:i',  strtotime($dealing->getTimeMonth()->format('Y-m-d H:i:s'))));
                    else
                        return date('Y/m/'.$dealing->getDayOfMonth().' '.date('H:i',  strtotime($dealing->getTimeMonth()->format('Y-m-d H:i:s'))),strtotime('+1 months'));
                    break;
                case 3 :
                    // array of minutes
                    $minutes = array('00',10,20,30,40,50);
                    $currentMinute = date('i');
                    foreach($minutes as $k => $min) {
                        if($min < $currentMinute)
                            unset($minutes[$k]);
                    }
                    $minutes = array_values($minutes);
                    if(!$minutes)
                        $futureMinute = '00';
                    else
                        $futureMinute = $minutes[0];
                    return date('Y/m/d H:'.$futureMinute);
                    
                    break;
            }
    }

}
