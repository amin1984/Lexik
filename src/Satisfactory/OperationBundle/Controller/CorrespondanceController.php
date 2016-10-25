<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Satisfactory\OperationBundle\Entity\Correspondance;
use Satisfactory\OperationBundle\Form\CorrespondanceType;
use Satisfactory\OperationBundle\Document\Correspondence;
use Satisfactory\OperationBundle\Document\EnterCorrespondence;
use Symfony\Component\HttpFoundation\Session\Session; 
use Satisfactory\SettingBundle\Entity\AppParam;

/**
 * Correspondance Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class CorrespondanceController extends Controller
{
    /**
     * Lists all Correspondance entities.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        return $this->render('SatisfactoryOperationBundle:Correspondance:index.html.twig');
    }
    
    /**
     * List of all Correspondance entities .
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxListAction(Request $request)
    {
        $IdCorrespondanceParam = $this->getParameter('IdCorrespondanceParam');
        
        $em = $this->getDoctrine()->getManager();
        
        $order = $request->get('order') ? $request->get('order') : 'id';
        
        $numPage = $request->get('page') ? $request->get('page') : 1;
        
        $orderType = $request->get('orderType') ? $request->get('orderType') : 'DESC';
        
        $input = $request->get('input') ? $request->get('input') : null;
        
        $client = $request->get('client') ? $request->get('client') : null;
        
        $correspondances = $em->getRepository('SatisfactoryOperationBundle:Correspondance')->paginatorQuery($limit=10, $offset=(($numPage-1)*$limit), $input, $client, $order, $orderType);
        
        $correspondancesLimitOff = $em->getRepository('SatisfactoryOperationBundle:Correspondance')->paginatorQueryLimitOff( (($numPage-1)*$limit), $input, $client, $order, $orderType);
        
        $countCorrespondances = count($correspondancesLimitOff);
        
        $clients = $em->getRepository('SatisfactoryUserBundle:User')->findByRole('ROLE_CLIENT');
        
        // Clients
        $arrayClients = array();
        $arrayClients[0]['id'] =  'all';
        $arrayClients[0]['name'] =  'Tous les clients';
        foreach ($clients as $key=>$value){
            $arrayClients[$key+1]['id'] =  $value->getId();
            $arrayClients[$key+1]['name'] =  $value->getUsername();
        }
        
        // Correspondances
        $arrayCorresp = array();
        foreach ($correspondances as $key=>$value){
            $arrayCorresp[$key]['id'] =  $value->getId();
            $arrayCorresp[$key]['name'] =  $value->getName();
            $arrayCorresp[$key]['client'] =  $value->getClient()->getUsername();
            $arrayCorresp[$key]['creatorName'] =  $value->getCreatorName();
            // updatedAt
            if ($value->getUpdatedAt())
                $arrayCorresp[$key]['updatedAt'] = $value->getUpdatedAt()->format('d-m-Y H:i:s');
            else
                $arrayCorresp[$key]['updatedAt'] = null;
            
            $arrayCorresp[$key]['pathEdit'] =  $this->generateUrl('operation_correspondance_edit',array('id' => $value->getId()));
            $arrayCorresp[$key]['pathDelete'] =  $this->generateUrl('operation_correspondance_delete',array('id' => $value->getId()));
        }
        
        return new Response(json_encode(array(
                    'correspondances' => $arrayCorresp,
                    'IdCorrespondanceParam' => $IdCorrespondanceParam,
                    'pages' => ceil($countCorrespondances / $limit) ,
                    'currentPage' => $request->get('page')>=1 ? (($request->get('page')-1)*$limit)+1 : 1,
                    'limit' => $limit,
                    'clients' => $arrayClients,
                    'offset' => $offset,
                )
        ));
        
    }

    /**
     * Creates a new Correspondance entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $correspondance = new Correspondance();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\CorrespondanceType', $correspondance);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //Save correspondance data
            $date = new \DateTime();
            $correspondance->setCreatedAt($date);
            $correspondance->setCreatedBy($this->getUser());
            $correspondance->setCreatorName($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            
            $em = $this->getDoctrine()->getManager();
            $correspondance->upload();
            //Persist and flush sql
            $em->persist($correspondance);
            
            
            // variable to determine if the given file is compatible withthe given correspondence informations.
            $uploadedFilsIsGood = true;
            
            /******************BEGIN TESTING THE UPPLOADED FILE****************************/
            $filename = $correspondance->getWebPath();
            if(file_exists($filename)) {
                $handle = fopen($filename,"r"); 
                // we take the first row of the file as a header and extract column form it.
                $headerCorrespondance = array();
                $count = 1;
                $rowsAreEqualToHeaders = TRUE;
                while (($row = fgetcsv($handle,0,";")) !== FALSE)
                {
                    if($count == 1) {
                        $headerCorrespondance = $row;
                        if(count($headerCorrespondance) != ($correspondance->getColumnNumber())) {
                            $uploadedFilsIsGood = FALSE;
                            $this->getRequest()->getSession()
                                ->getFlashBag()
                                ->add('correspondance_uploaded_file_error', $this->get('translator')->trans('Fichier correspondance non compatible. Erreur nombre de colonnes'))
                            ;
                            break 1;
                        }
                    }
                    if(count($row) != ($correspondance->getColumnNumber())) {
                        $uploadedFilsIsGood = FALSE;
                        $this->getRequest()->getSession()
                            ->getFlashBag()
                            ->add('correspondance_uploaded_file_error', $this->get('translator')->trans('Fichier correspondance non compatible. Erreur nombre de colonnes dans la ligne %line%',array('%line%'=>$count)));
                        ;
                        break 1;
                    }
                    $count++;
                }
            }
            /******************END TEST UPLOADED FILE****************************/
            if($uploadedFilsIsGood) {
                $em->flush();
                //Save correspondance data in mongoDb
                $document = new Correspondence();
                $date = new \DateTime();
                $document->setIndex($correspondance->getId());
                $document->setFile($correspondance->getPath());
                $document->setCreatedAt($date);
                $document->setCreatedBy($this->getUser());
                $document->setClient($correspondance->getClient()->getUsername());
                $document->setName($correspondance->getName());
                $dm = $this->get('doctrine_mongodb')->getManager();
                //Persist and flush mongoDb
                $dm->persist($document);
                $dm->flush();

                /********************* SAVE ENTERS FROM THE UPLOADED FILE**********************/
                $this->saveEnters($correspondance);
                return $this->redirectToRoute('operation_correspondance_index');
            }   
        }

        return $this->render('SatisfactoryOperationBundle:Correspondance:new.html.twig', array(
            'correspondance' => $correspondance,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Correspondance entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Correspondance entity $correspondance
     */
    public function editAction(Request $request, Correspondance $correspondance)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\CorrespondanceType', $correspondance);
        $form->handleRequest($request);
        $uploadedFilsIsGood = true;
        $IdCorrespondanceParam = $this->getParameter('IdCorrespondanceParam');
        

        /*because AppParam must contain only one row, so we query with find()
         * If AppParam change then we have to change the below line to get the 
         * correct parameters
         */
        $AppParam = $em->getRepository('SatisfactorySettingBundle:AppParam')->find(1);
        // special code for the PAram correspodence
        if($correspondance->getId() == $IdCorrespondanceParam)
            $correspondance->setName($AppParam->getIdParamErdf());
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $date = new \DateTime();
            
            // special code for the PAram correspodence
            if($correspondance->getId() == $IdCorrespondanceParam)
                $correspondance->setName($AppParam->getIdParamErdf());
            
            $correspondance->setUpdatedAt($date);
            $correspondance->setUpdatedBy($this->getUser());
            $correspondance->upload();
            //Save correspondance data in mongoDb
            $dm = $this->get('doctrine_mongodb')->getManager();
//            $document = $dm->getRepository('SatisfactoryOperationBundle:Correspondence')->findBy(array('index' => $correspondance->getId()));
//            $document = $dm->getRepository('SatisfactoryOperationBundle:Correspondence')->find($document[0]->getId());
//            $document->setClient($correspondance->getClient()->getUsername());
//            $document->setName($correspondance->getName());
//            $document->setUpdatedAt($correspondance->getUpdatedAt());
            
            // variable to determine if the given file is compatible withthe given correspondence informations.
            $uploadedFilsIsGood = true;
            
            /******************BEGIN TESTING THE UPPLOADED FILE****************************/
            $filename = $correspondance->getWebPath();
            if(file_exists($filename)) {
                $handle = fopen($filename,"r"); 
                // we take the first row of the file as a header and extract column form it.
                $headerCorrespondance = array();
                $count = 1;
                $rowsAreEqualToHeaders = TRUE;
                while (($row = fgetcsv($handle,0,";")) !== FALSE)
                {
                    if($count == 1) {
                        $headerCorrespondance = $row;
                        if(count($headerCorrespondance) != ($correspondance->getColumnNumber())) {
                            $uploadedFilsIsGood = FALSE;
                            $this->getRequest()->getSession()
                                ->getFlashBag()
                                ->add('correspondance_uploaded_file_error', $this->get('translator')->trans('Fichier correspondance non compatible. Erreur nombre de colonnes'))
                            ;
                            break 1;
                        }
                    }
                    if(count($row) != ($correspondance->getColumnNumber())) {
                        $uploadedFilsIsGood = FALSE;
                        $this->getRequest()->getSession()
                            ->getFlashBag()
                            ->add('correspondance_uploaded_file_error', $this->get('translator')->trans('Fichier correspondance non compatible. Erreur nombre de colonnes dans la ligne %line%',array('%line%'=>$count)));
                        ;
                        break 1;
                    }
                    $count++;
                }
            }
            /******************END TEST UPLOADED FILE****************************/
            if($uploadedFilsIsGood) {
                //Persist and flush mongoDb
                $em->persist($correspondance);
                $em->flush();
//                $dm->persist($document);
//                $dm->flush();

                /********************* DELETE ENTERS IF OPTIONS REMOVING IS CHECKED**********************/
                if($correspondance->getReplacing()==1) {
                    // /!\ TO IMPROVE : WE NEED TO ASK FOR DELETION BEFORE "foreach" OR WHATEVER !!!
                    //$qb = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->createQueryBuilder('EnterCorrespondence');                     
                    //$qb->field('correspondenceId')->equals($correspondenceId);
                    
//                    $qb = $dm->createQueryBuilder('SatisfactoryOperationBundle:EnterCorrespondence');
//                    $qb->remove()
//                        ->field('correspondenceId')->equals($correspondance->getId())
//                        ->getQuery()
//                        ->execute();
                    
                    $m = $this->container->get('doctrine_mongodb.odm.default_connection');
                    // select a database
                    $db = $m->selectDatabase('satisfactory');

                    // select a collection (analogous to a relational database's table)
                    $collection = $db->selectCollection('TDC_'.$correspondance->getId());
                    if($correspondance->getId() == $this->getParameter('IdCorrespondanceParam')) {
                        $collection = $db->selectCollection($AppParam->getIdParamErdf());
                    }
                    $collection->drop();
                    /*
                    $documents = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findByCorrespondence($correspondance->getId());
                    if($documents) {
                        foreach ($documents as $d) {
                            $dm->remove($d);
                        }
                        $dm->flush();
                    } */
                }
                /********************* SAVE ENTERS FROM THE UPLOADED FILE**********************/
                
                if($_FILES['correspondance']['name']['file'] != "")
                    $this->saveEnters($correspondance,$AppParam->getIdParamErdf());
                
                return $this->redirectToRoute('operation_correspondance_index');
            }
        }
        //EnterCorrespondence list
        $dm = $this->get('doctrine_mongodb')->getManager();
        $count = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->findCountByCorrespondence($correspondance->getId(),$AppParam->getIdParamErdf());
        

        return $this->render('SatisfactoryOperationBundle:Correspondance:edit.html.twig', array(
            'correspondance' => $correspondance,
            'form' => $form->createView(),
            'countCorrespondence' => $count,
            'filepath' => $correspondance->getWebPath(),
            'findPath' => file_exists($correspondance->getWebPath()),
            'IdCorrespondanceParam' => $IdCorrespondanceParam,
            
        ));
    }

    /**
     * Deletes a Correspondance entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Correspondance entity $correspondance
     */
    public function deleteAction(Request $request, Correspondance $correspondance)
    {
        if (!$correspondance)
        {
            throw $this->createNotFoundException('Unable to find correspondance entity.');
        } 
        
        if ($request->getMethod() == 'GET') {
            //Delete from mongo
            $dm = $this->get('doctrine_mongodb')->getManager();
            $document = $dm->getRepository('SatisfactoryOperationBundle:Correspondence')->findBy(array('index' => $correspondance->getId()));
            $document = $dm->getRepository('SatisfactoryOperationBundle:Correspondence')->find($document[0]->getId());
            $dm->remove($document);
            $dm->flush();
            //Delete from SQL
            $em = $this->getDoctrine()->getManager();
            $em->remove($correspondance);
            $em->flush();
        }

        return $this->redirectToRoute('operation_correspondance_index');
    }
    
    protected function saveEnters($correspondance,$correspondanceName = NULL)
    {
        if(!$correspondance) {
            return false;
        }
        $filename = $correspondance->getWebPath();
        if(!file_exists($filename)) {
            return false;
        }
        $handle = fopen($filename,"r"); 
        $count = 1;
        $headerCorrespondance = $output = $output = array();
        $date   = new \DateTime();
        $em     = $this->getDoctrine()->getManager();
        $dm     = $this->get('doctrine_mongodb')->getManager();
        $author = $this->getUser()->getLastName()." ".$this->getUser()->getFirstName();
        $cid    = $correspondance->getId();
                        
        $m          = $this->container->get('doctrine_mongodb.odm.default_connection');
        // select a database
        $db         = $m->selectDatabase('satisfactory');
        $collection = $db->createCollection('TDC_'.$cid); 
        if($correspondance->getId() == $this->getParameter('IdCorrespondanceParam')) 
            $collection = $db->selectCollection($correspondanceName);
        
        while (($row = fgetcsv($handle,0,";")) !== FALSE)
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
            }
            if(count($row) == ($correspondance->getColumnnumber()) && $count > 1) {
                #####################################################################
                ##This eliminate every wrong row from the csv, TO VERIFY THIS LATER##
                ###It will make sure that will save only rows with only X values####
                #####################################################################

                $document = array();
                for($i=0;$i < count($row);$i++) {
                    $document[$headerCorrespondance[$i]] = trim($row[$i]);
                    $document[$headerCorrespondance[$i]] = (preg_match('!!u', $row[$i])) ? $row[$i] : utf8_encode($row[$i]);
                }
                $document['correspondenceId'] = $cid;
                $document['createdAt'] = $date->format('Y-m-d H:i:s');
                $document['createdBy'] = $author;
                
                $collection->insert($document);
            }
            $count++;
        }
        $dm->flush();
    }
    
    /**
     * Get Columns Correspondance entitie.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxColumnsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $correspendance = $em->getRepository('SatisfactoryOperationBundle:Correspondance')->find($request->get('id'));
       
        $session = $request->getSession();
        $session->set('correspondanceId', $request->get('id'));
        
        $response = new Response(json_encode($correspendance->getColumns()));

        return $response;
    }
}
