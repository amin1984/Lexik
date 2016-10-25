<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

use Satisfactory\SettingBundle\Entity\Setting;
use Satisfactory\SettingBundle\Entity\Quest;
use Satisfactory\SettingBundle\Entity\Group;
use Satisfactory\SettingBundle\Form\SettingType;
use Symfony\Component\HttpFoundation\Session\Session;
use Satisfactory\CronBundle\Repository\ParamGroupesRepository;
use Satisfactory\SettingBundle\Entity\AppParam;
use Satisfactory\SettingBundle\Form\AppParamType;



/**
 * Setting controller.
 *
 * @author Arous Amin <amin@celaneo.com>    
 */
class SettingController extends Controller
{
    /**
     * Lists all Setting entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agencys = $em->getRepository('SatisfactorySettingBundle:Agency')->findAll();
        
        // Session: group_id
        $session = new Session();
        $session->set('group_id', $request->get('group_id'));

        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findAll();
        
        // Parameter connexion
        $AppParam = $em->getRepository('SatisfactorySettingBundle:AppParam')->find(1);
        $paramId = $AppParam->getIdParamErdf();
        $m = $this->container->get('doctrine_mongodb.odm.default_connection');
        // select a database
        $db = $m->selectDatabase('satisfactory');
        $collection = $db->selectCollection($paramId);
        $param = $collection->findOne(array('groupId' => (int)$request->get('group_id') ));
        
        if($param)
            $connexion = 1;
        else 
           $connexion = 0; 
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $settings, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );
            
        return $this->render('SatisfactorySettingBundle:Setting:index.html.twig', array(
            'settings' => $settings,
            'connexion' => $connexion,
            'agencys' => $agencys,
            'selectedAgency' => null,
            'paramName' => null,
            'date' => date('Y-m-d'),
            'status' => null,
            'pagination' => $pagination,
        ));
    }
    
    /**
     * Ajax list of all setting .
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $order = $request->get('order') ? $request->get('order') : 'name';
        
        $numPage = $request->get('page') ? $request->get('page') : 1;
        
        $orderType = $request->get('orderType') ? $request->get('orderType') : 'DESC';
        
        $input = $request->get('input') ? $request->get('input') : null;
        
        $agency = $request->get('agency') ? $request->get('agency') : 'all';
        
        $status = $request->get('status') ? $request->get('status') : null;
        
        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->paginatorQuery($limit=10, $offset=(($numPage-1)*$limit), $input, $agency, $order, $orderType, $status);
        
        $settingsLimitOff = $em->getRepository('SatisfactorySettingBundle:Setting')->paginatorQueryLimitOff( (($numPage-1)*$limit), $input, $agency, $order, $orderType, $status);
        
        $countSettings = count($settingsLimitOff);
        
        $agencys = $em->getRepository('SatisfactorySettingBundle:Agency')->findAll();
        
        // Agencys
        $arrayAgencys = array();
        $arrayAgencys[0]['id'] =  'all';
        $arrayAgencys[0]['name'] =  'Toutes les agences';
        foreach ($agencys as $key=>$value){
            $arrayAgencys[$key+1]['id'] =  $value->getId();
            $arrayAgencys[$key+1]['name'] =  $value->getName();
        }
        
        // Status
        $arrayStatus = array();
        $arrayStatus[0]['value'] =  0;
        $arrayStatus[0]['type'] =  'Tous les statuts';
        $arrayStatus[1]['value'] =  1;
        $arrayStatus[1]['type'] =  'Enquête en cours';
        $arrayStatus[2]['value'] =  2;
        $arrayStatus[2]['type'] =  'Enquêtes non débutées';
        $arrayStatus[3]['value'] =  3;
        $arrayStatus[3]['type'] =  'Enquêtes cloturées';
        
        // Settings
        $arraySettings = array();
        foreach ($settings as $key=>$value){
            $arraySettings[$key]['id'] =  $value->getId();
            $arraySettings[$key]['name'] =  $value->getName();
            $arraySettings[$key]['quest'] =  $value->getQuest()->getName();
            $arraySettings[$key]['createdBy'] =  $value->getCreatedBy();
            
            // Agencys
            $arrayAgency = array();
            foreach ($value->getAgency() as $k=>$val){
                $arrayAgency[$k]['name'] =  $val->getName();
            }
            $arraySettings[$key]['agencys'] =  $arrayAgency;
            
            // updatedAt
            if ($value->getUpdatedAt())
                $arraySettings[$key]['updatedAt'] = $value->getUpdatedAt()->format('d-m-Y H:i:s');
            else
                $arraySettings[$key]['updatedAt'] = null;
            
            if (($value->getDateBegin()->format('Y-m-d') <= date('Y-m-d')) and ( date('Y-m-d') <= $value->getDateEnd()->format('Y-m-d') ))
                $arraySettings[$key]['status'] = 'Enquête en cours';
            elseif ($value->getDateBegin()->format('Y-m-d') > date('Y-m-d'))
                $arraySettings[$key]['status'] = 'Enquêtes non débutées';
            elseif ($value->getDateEnd()->format('Y-m-d') < date('Y-m-d'))
                $arraySettings[$key]['status'] = 'Enquêtes cloturées';
            
            // Paths
            
            $arraySettings[$key]['pathShow'] =  $this->generateUrl('setting_show',array('id' => $value->getId()));
            $arraySettings[$key]['pathDelete'] =  $this->generateUrl('setting_delete',array('id' => $value->getId()));
            $arraySettings[$key]['pathEdit'] =  $this->generateUrl('setting_edit',array('id' => $value->getId()));
            $arraySettings[$key]['pathCopy'] =  $this->generateUrl('setting_copy',array('id' => $value->getId()));
        }
        
        return new Response(json_encode(array(
                    'settings' => $arraySettings,
                    'pages' => ceil($countSettings / $limit) ,
                    'currentPage' => $request->get('page')>=1 ? (($request->get('page')-1)*$limit)+1 : 1,
                    'limit' => $limit,
                    'agencys' => $arrayAgencys,
                    'status' => $arrayStatus,
                    'offset' => $offset
                )
        ));
        
    }
    
     /**
     * Search in Setting entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agencys = $em->getRepository('SatisfactorySettingBundle:Agency')->findAll();
        $paramName = null;
        $agency = null;
        $status = null;
        
        // Session: group_id
        $session = new Session();
        $session->set('group_id', $request->get('group_id'));

        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findAll();
        
        if ($request->getMethod() == 'POST') {
            $paramName = $request->get('paramName');
            $agency = $request->get('agency');
            $status = $request->get('status');
            $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findBySearch($paramName, $agency, $status);
        }

         // Parameter connexion
        $AppParam = $em->getRepository('SatisfactorySettingBundle:AppParam')->find(1);
        $paramId = $AppParam->getIdParamErdf();
        $m = $this->container->get('doctrine_mongodb.odm.default_connection');
        // select a database
        $db = $m->selectDatabase('satisfactory');
        $collection = $db->selectCollection($paramId);
        $param = $collection->findOne(array('groupId' => (int)$request->get('group_id') ));
        
        if($param)
            $connexion = 1;
        else 
           $connexion = 0; 
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $settings, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );
            
        return $this->render('SatisfactorySettingBundle:Setting:index.html.twig', array(
            'settings' => $settings,
            'connexion' => $connexion,
            'agencys' => $agencys,
            'selectedAgency' => $agency,
            'paramName' => $paramName,
            'status' => $status,
            'date' => date('Y-m-d'),
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new Setting entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        $groupId =   $session->get('group_id');
        $setting = new Setting();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\SettingType', $setting);
        $form->handleRequest($request);
        
        $requestForm = $request->get('setting');
        if(isset($requestForm ["agency"]))
          $requestAgency = $form ["agency"]->getData();
        else
          $requestAgency = null; 
        if(isset($requestForm ["segment"]))
          $requestSegment = $form ["segment"]->getData();
        else            
            $requestSegment = null; 
       
        if ($form->isSubmitted()) {
                $existingAgencies = $em->getRepository('SatisfactorySettingBundle:Setting')->findBySearchCorrespond(null,$requestAgency,$requestSegment,$form ["quest"]->getData(),$form ["dateBegin"]->getData(),$form ["dateEnd"]->getData()); 
                if($existingAgencies) {
                       $this->get('session')->getFlashBag()->add(
                               'alreadyExistError',
                               $this->get('translator')->trans('alreadyExistError %agencies%',array('%agencies%'=>$existingAgencies))   
                    );

                }
                else {
                    if(!$requestAgency){
                        $form->get('agency')->addError(new FormError('Veuillez sélectionner au moins une agence'));
                    }
                    if(!$requestSegment){
                        $form->get('segment')->addError(new FormError('Veuillez sélectionner au moins un ségment'));
                    }
                    if($form ["dateBegin"]->getData() > $form ["dateEnd"]->getData()){
                        $form->get('dateEnd')->addError(new FormError('La date de fin ne doit pas être antérieure à la date de début'));
                    }

            }
            if($form->isValid() && !$existingAgencies) {

                    $setting->setGroupId($groupId);
                    $setting->setCreatedAt(new \DateTime());
                    $setting->setUpdatedAt(new \DateTime());
                    $group = $em->getRepository('SatisfactorySettingBundle:Group')->findOneBy(array('group_id'=>$groupId));
                    
                    if($group) {
                        $setting->setCreatedBy($group->getName());
                    }
                    $em->persist($setting);
                    $em->flush();

                    return $this->redirectToRoute('setting_index', array('group_id' => $groupId));

            }
        }

        return $this->render('SatisfactorySettingBundle:Setting:new.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
            'groupId' => $groupId,    
        ));
    }

    /*
     * return array of Setting objects or false
     */
    private function isParametrageExists ($requestForm) {
        if(isset($requestForm ["agency"]))
          $requestAgency = $requestForm ["agency"];
        else
          $requestAgency = null; 
        if(isset($requestForm ["segment"]))
          $requestSegment = $requestForm ["segment"];
        else
          $requestSegment = ""; 
        
        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findBySearch($paramName, $agency, $status);
    }
    /**
     * Finds and displays a Setting entity.
     *
     */
    public function showAction(Setting $setting)
    {
        $session = new Session();
        $groupId =   $session->get('group_id');
        
        $form = $this->createForm('Satisfactory\SettingBundle\Form\SettingType', $setting);
        
        return $this->render('SatisfactorySettingBundle:Setting:show.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
            'groupId' => $groupId, 
        ));
    }

    /**
     * Displays a form to edit an existing Setting entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function editAction(Request $request, Setting $setting)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        $groupId =   $session->get('group_id');
        $form = $this->createForm('Satisfactory\SettingBundle\Form\SettingType', $setting);
        $form->handleRequest($request);
        
        $requestForm = $request->get('setting');
        if(($requestForm->getAgency()))
          $requestAgency = $form ["agency"]->getData();
        else
          $requestAgency = null; 
        if(($requestForm ->getSegment()))
          $requestSegment = $form ["segment"]->getData();
        else            
            $requestSegment = null; 
       
        if ($form->isSubmitted()) {
                $existingAgencies = $em->getRepository('SatisfactorySettingBundle:Setting')->findBySearchCorrespond(null,$requestAgency,$requestSegment,$form ["quest"]->getData(),$form ["dateBegin"]->getData(),$form ["dateEnd"]->getData(),$setting); 
                if($existingAgencies) {
                       $this->get('session')->getFlashBag()->add(
                               'alreadyExistError',
                               $this->get('translator')->trans('alreadyExistError %agencies%',array('%agencies%'=>$existingAgencies))   
                    );

                }
                else {
                    if(count($requestAgency) <1){
                        $form->get('agency')->addError(new FormError('Veuillez sélectionner au moins une agence'));
                    }
                    if(count($requestSegment) <1){
                        $form->get('segment')->addError(new FormError('Veuillez sélectionner au moins un ségment'));
                    }
                    if($form ["dateBegin"]->getData() > $form ["dateEnd"]->getData()){
                        $form->get('dateEnd')->addError(new FormError('La date de fin ne doit pas être antérieure à la date de début'));
                    }

            }
            if($form->isValid() && !$existingAgencies) {

                    $setting->setGroupId($groupId);
                    $setting->setUpdatedAt(new \DateTime());

                    $em->persist($setting);
                    $em->flush();

                    return $this->redirectToRoute('setting_index', array('group_id' => $groupId));

            }
        }

        return $this->render('SatisfactorySettingBundle:Setting:edit.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
            'groupId' => $groupId, 
        ));
    }

    /**
     * Deletes a Setting entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $setting
     */
    public function deleteAction(Request $request, Setting $setting)
    {
        if (!$setting)
        {
            throw $this->createNotFoundException('Unable to find dealing entity.');
        } 
        $session = new Session();
        $groupId =   $session->get('group_id');
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($setting);
            $em->flush();
        }

        return $this->redirectToRoute('setting_index',array('group_id' => $groupId));
    }
    
    /**
     * Generate csv.
     * 
     */
    public function generateExcelAction()
    {
        $em = $this->getDoctrine()->getManager();
//        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findBy(array('user' => $this->getUser()));
        $settings = $em->getRepository('SatisfactorySettingBundle:Setting')->findAll();
        
        // ask the service for a CSV
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio");
        
        $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', ($this->get('translator')->trans('Nom du parametrage')))
                ->setCellValue('B1',('Enquête'))
                ->setCellValue('C1',('Agence'))
                ->setCellValue('D1',('Créé par'))
                ->setCellValue('E1',('status')) ;
        
        foreach($settings as $key=>$setting)
        {
          $key = $key+2;
          $agencyList = null;
          $status = null;
          foreach($setting->getAgency() as $agency)
          {
             $agencyList =  $agencyList." ".$agency;
          }
          if( $setting->getDateEnd()->format('Y-m-d') >= date('Y-m-d') && $setting->getDateBegin()->format('Y-m-d') <= date('Y-m-d') )
              $status = $this->get('translator')->trans("Enquêtes en cours");
          elseif($setting->getDateBegin()->format('Y-m-d') > date('Y-m-d'))
              $status = "Enquêtes non débutées";
          elseif($setting->getDateEnd()->format('Y-m-d') < date('Y-m-d'))
              $status = $this->get('translator')->trans("Enquêtes cloturées");
          $groupName="";
            $group = $em->getRepository('SatisfactorySettingBundle:Group')->findOneBy(array('group_id'=>$setting->getGroupId()));

            if($group) {
                $groupName = ($group->getName());
            }
          $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$key, ($setting->getName()))
                ->setCellValue('B'.$key, ($setting->getQuest()->getName()))
                ->setCellValue('C'.$key,($agencyList))
                ->setCellValue('D'.$key, $groupName)
                ->setCellValue('E'.$key,($status))  ;
        }
        
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'CSV');
        $writer->setDelimiter(";");
        echo "\xEF\xBB\xBF";
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, 
                date('Y').date('m').date('d').'-erdf-liste-parametrages.csv'
        );
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
    
    /**
     * Copy Setting entity
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $setting
     */
    public function copyAction(Request $request, Setting $setting)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $settingCopy = clone $setting;
            $settingCopy->setName($request->get('name'));
            $settingCopy->setUpdatedAt(new \DateTime());
            $em->persist($settingCopy);
            $em->flush();
            
            $session = new Session();
            return $this->redirectToRoute('setting_index', array('group_id' => $session->get('group_id')));
        }

        return $this->render('SatisfactorySettingBundle:Setting:copy.html.twig', array(
            'setting' => $setting 
        ));
    }
    
    /**
     * Creates a new application Setting.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function applicationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $appParam = $em->getRepository('SatisfactorySettingBundle:AppParam')->find(1);
        $form = $this->createForm('Satisfactory\SettingBundle\Form\AppParamType', $appParam);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
                $this->get('session')->getFlashBag()->add('successAppParam', 'Enregistrement effectué avec succès');
                $em->persist($appParam);
                $em->flush(); 
        }

        return $this->render('SatisfactorySettingBundle:Setting:application.html.twig', array(
            'appParam' => $appParam,
            'form' => $form->createView(),  
        ));
    }
    
}
