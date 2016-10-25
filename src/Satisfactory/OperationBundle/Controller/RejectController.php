<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Satisfactory\OperationBundle\Entity\Reject;
use Satisfactory\OperationBundle\Form\RejectType;

/**
 * 
 * Reject controller.
 *
 * @author Arous Amin <amin@celaneo.com>
 */
class RejectController extends Controller
{
    /**
     * Lists all Reject entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $rejects = $em->getRepository('SatisfactoryOperationBundle:Reject')->findAll();
        
        $clients = $em->getRepository('SatisfactoryUserBundle:User')->findByRole('ROLE_CLIENT');
        
        $expression = "";
        
        $client = 'all';
        
        $rejectType = 'all';
        
        if($request->getMethod() == 'POST'){
            
            if ($request->get('client') != 'all')
                $client = $em->getRepository('SatisfactoryUserBundle:User')->find($request->get('client'));

            $rejects = $em->getRepository('SatisfactoryOperationBundle:Reject')->findBySearch($client, $request->get('type'), $request->get('expression'));
            
            $expression = $request->get('expression');
            
            $rejectType = $request->get('type');
        }
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $rejects, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );

        return $this->render('SatisfactoryOperationBundle:Reject:index.html.twig', array(
                    'rejects' => $rejects,
                    'clients' => $clients,
                    'selectedClient' => $request->get('client'),
                    'expression' => $expression,
                    'rejectType' => $rejectType,
                    'pagination' => $pagination,
        ));
    }
    
     /**
     * List ajax of all Rejects.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $order = $request->get('order') ? $request->get('order') : 'id';
        
        $numPage = $request->get('page') ? $request->get('page') : 1;
        
        $orderType = $request->get('orderType') ? $request->get('orderType') : 'DESC';
        
        $input = $request->get('input') ? $request->get('input') : null;
        
        $client = $request->get('client') ? $request->get('client') : null;
        
        $type = $request->get('type') ? $request->get('type') : null;
        
        $rejects = $em->getRepository('SatisfactoryOperationBundle:Reject')->paginatorQuery($limit=10, $offset=(($numPage-1)*$limit), $input, $client, $order, $orderType, $type);
        
        $rejectsLimitOff = $em->getRepository('SatisfactoryOperationBundle:Reject')->paginatorQueryLimitOff( (($numPage-1)*$limit), $input, $client, $order, $orderType, $type);
        
        $countRejects = count($rejectsLimitOff);
        
        $clients = $em->getRepository('SatisfactoryUserBundle:User')->findByRole('ROLE_CLIENT');
        
        // Clients
        $arrayClients = array();
        $arrayClients[0]['id'] =  'all';
        $arrayClients[0]['name'] =  'Tous les clients';
        foreach ($clients as $key=>$value){
            $arrayClients[$key+1]['id'] =  $value->getId();
            $arrayClients[$key+1]['name'] =  $value->getUsername();
        }
        
        // Rejects
        $array = array();
        foreach ($rejects as $key=>$value){
            $array[$key]['id'] =  $value->getId();
            $array[$key]['name'] =  $value->getRuleName();
            $array[$key]['client'] =  $value->getClient()->getUsername();
            $array[$key]['creatorName'] =  $value->getCreatorName();
            $array[$key]['type'] =  $value->getType();
            // updatedAt
            if ($value->getUpdatedAt())
                $array[$key]['updatedAt'] = $value->getUpdatedAt()->format('d-m-Y H:i:s');
            else
                $array[$key]['updatedAt'] = null;
            
            $operation =  $em->getRepository('SatisfactoryOperationBundle:Operation')->findBy(array('reject' => $value->getId()));
            // Operations list
            $arrayOperations = array();
            foreach ($operation as $k=>$op){ 
              $arrayOperations[$k]['id'] = $op->getId();
              $arrayOperations[$k]['name'] = $op->getDescription();
              $arrayOperations[$k]['pathOperation'] =  $this->generateUrl('operation_operation_edit',array('id' => $op->getId()));
            }
            $array[$key]['operations'] = $arrayOperations;
            
            $array[$key]['pathEdit'] =  $this->generateUrl('operation_reject_edit',array('id' => $value->getId()));
            $array[$key]['pathDelete'] =  $this->generateUrl('operation_reject_delete',array('id' => $value->getId()));
        }
        
        return new Response(json_encode(array(
                    'rejects' => $array,
                    'pages' => ceil($countRejects / $limit) ,
                    'currentPage' => $request->get('page')>=1 ? (($request->get('page')-1)*$limit)+1 : 1,
                    'limit' => $limit,
                    'clients' => $arrayClients,
                    'offset' => $offset,
                )
        ));
        
    }

    /**
     * Creates a new Reject entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $reject = new Reject();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\RejectType', $reject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Save reject data
            $date = new \DateTime();
            $reject->setCreatedAt($date);
            $reject->setCreatedBy($this->getUser());
            $reject->setCreatorName($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            
            $em = $this->getDoctrine()->getManager();
            $reject->upload();
            $em->persist($reject);
            $em->flush();

            return $this->redirectToRoute('operation_reject_index');
        }

        return $this->render('SatisfactoryOperationBundle:Reject:new.html.twig', array(
            'reject' => $reject,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reject entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Reject entity $reject
     */
    public function editAction(Request $request, Reject $reject)
    {
        $editForm = $this->createForm('Satisfactory\OperationBundle\Form\RejectType', $reject);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //Save reject data
            $date = new \DateTime();
            $reject->setUpdatedAt($date);
            $reject->setUpdatedBy($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $reject->upload();
            $em->persist($reject);
            $em->flush();

            return $this->redirectToRoute('operation_reject_index');
        }
        $fileRejetc = $reject->getWebPath();
        $totalLines = 0;
        if(is_file($fileRejetc))
            $totalLines = intval(exec("wc -l '$fileRejetc'"));
        return $this->render('SatisfactoryOperationBundle:Reject:edit.html.twig', array(
            'reject' => $reject,
            'fileRejetc' => $fileRejetc,
            'totalLines' => $totalLines,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Reject entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Reject entity $reject
     */
    public function deleteAction(Request $request, Reject $reject)
    {
        if (!$reject)
        {
            throw $this->createNotFoundException('Unable to find Reject entity.');
        } 
        
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reject);
            $em->flush();
        }

        return $this->redirectToRoute('operation_reject_index');
    }
    
}
