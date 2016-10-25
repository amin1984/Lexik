<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\OperationBundle\Entity\Operation;
use Satisfactory\OperationBundle\Entity\Concat;
use Satisfactory\OperationBundle\Entity\Replacement;
use Satisfactory\OperationBundle\Form\OperationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Controller Operation Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */
class OperationController extends Controller 
{

    /**
     * Creates a new Dealing entity.
     * 
     * @param integer $id (Id of dealing)
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();
        $operations = $em->getRepository('SatisfactoryOperationBundle:Operation')->findAll();
        $dealing = $em->getRepository('SatisfactoryOperationBundle:Dealing')->find($id);
        //Count Archive and publish operation
        $archiveOperation = $em->getRepository('SatisfactoryOperationBundle:Operation')->findBy(array('type' => 'Archive', 'dealing' => $dealing));
        $publishOperation = $em->getRepository('SatisfactoryOperationBundle:Operation')->findBy(array('type' => 'Publish', 'dealing' => $dealing));

        $operation = new Operation();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\OperationType', $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //save operation data
            $date = new \DateTime();
            $operation->setCreatedAt($date);
            $operation->setCreatedBy($this->getUser());
            $operation->setDealing($dealing);
            $operation->setStatus(1);
            //save order
            $countOperations = count($operations);
            if($operation->getType() == 'Rule')
                $operation->setOrderItem(++$countOperations+1000);
            elseif($operation->getType() == 'Publish')
                $operation->setOrderItem(++$countOperations+2000);
            elseif($operation->getType() == 'Archive') {
                $operation->setOrderItem(++$countOperations+3000);
                $operation->setStatus(1);
            }
            else
            $operation->setOrderItem($countOperations + 1);
            
            // specific code for filter
            if($operation->getJson()) {
                $json = $operation->getJson();
                $json = str_replace('{ {', '{{', $json);
                $json = str_replace('} }', '}}', $json);
                $operation->setJson($json);
            }
            $em->persist($operation);

            $em->flush();

            return $this->redirectToRoute('operation_operation_edit', array('id' => $operation->getId()));
        }
        
        return $this->render('SatisfactoryOperationBundle:Operation:new.html.twig', array(
                    'dealing' => $dealing,
                    'operation' => $operation,
                    'dealingId' => $id,
                    'form' => $form->createView(),
                    'countArchive' => count($archiveOperation),
                    'countPublish' => count($publishOperation),
        ));
    }

    /**
     * Creates a new Dealing entity.
     * 
     * @param entity Operation $operation
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function editAction(Request $request, Operation $operation) 
    {
        if ($operation->getType() == 'Filter')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationFilterType', $operation);
        elseif ($operation->getType() == 'Table')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationTableType', $operation);
        elseif ($operation->getType() == 'Modify')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationModifyType', $operation);
        else if ($operation->getType() == 'Enrich')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationEnrichType', $operation);
        else if ($operation->getType() == 'Dedoublonner')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationDuplicateType', $operation);
        else if ($operation->getType() == 'Rejected')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationRejectType', $operation);
        else if ($operation->getType() == 'Publish')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationPublishType', $operation);
        else if ($operation->getType() == 'Rule')
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationFilteringClientType', $operation);
        else if ($operation->getType() == 'Archive')
            return $this->redirectToRoute('operation_dealing_edit', array('id' => $operation->getDealing()->getId()));
        else if ($operation->getType() == 'Concat') {
            $concat = new Concat();
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationConcatenateType',$concat);
            $editFormOperation = $this->createForm('Satisfactory\OperationBundle\Form\OperationConcatType',$operation);
        }
        else if ($operation->getType() == 'Replacement') {
            $replacement = new Replacement();
            $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationReplacementType',$replacement);
            $editFormOperation = $this->createForm('Satisfactory\OperationBundle\Form\OperationReplaceType',$operation);
        }
       
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            if($request->get('check_list'))
                $operation->setColumnsChecked ($request->get('check_list'));
            if($request->get('json'))
                $operation->setJson ($request->get('json'));
            if($request->get('jsonCorrespondence'))
                $operation->setJsonCorrespondence ($request->get('jsonCorrespondence'));
            if($request->get('targetColumn'))
                $operation->setTargetColumn (trim($request->get('targetColumn')));
                
            //save operation data
            $date = new \DateTime();
            $operation->setCreatedAt($date);
            $operation->setCreatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            // specific code for filter
            if($operation->getJson()) {
                $json = $operation->getJson();
                $json = str_replace('{ {', '{{', $json);
                $json = str_replace('} }', '}}', $json);
                $operation->setJson($json);
            }
            
            // If Contenate
            if ($operation->getType() == 'Concat'){
                $concat->setOperation($operation);
                $requestOperation = $request->get('operation_concat');
                // Operation Desciption
                $operation->setDescription($requestOperation['description']);
                $em->persist($operation);
                $em->persist($concat);
            }  
            // If Replacement
            elseif ($operation->getType() == 'Replacement'){
                $replacement->setOperation($operation);
                $requestOperation = $request->get('operation_replace');
                // Operation Desciption
                echo "<pre>";
                print_r($requestOperation);
                echo "</pre>";
                $operation->setDescription($requestOperation['description']);
                $em->persist($operation);
                $em->persist($replacement);
            }   
            else
                $em->persist($operation);

            $em->flush();
            
            if ($operation->getType() != 'Concat') {
                return $this->redirectToRoute('operation_dealing_edit', array('id' => $operation->getDealing()->getId()));
                $this->get('session')->getFlashBag()->add('noticeConcat', 'Enregistrement effectué avec succès');
            } else {
                $this->get('session')->getFlashBag()->add('notice', 'Enregistrement effectué avec succès');
                return $this->redirectToRoute('operation_operation_edit', array('id' => $operation->getId()));
            }
        }
        
        if($operation->getType() == 'Filter') {
            return $this->render('SatisfactoryOperationBundle:Operation:editFilter.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));
        }
        elseif ($operation->getType() == 'Table') {
            return $this->render('SatisfactoryOperationBundle:Operation:editTable.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));
        }else if ($operation->getType() == 'Modify') {
            return $this->render('SatisfactoryOperationBundle:Operation:editModify.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));
        } else if ($operation->getType() == 'Enrich') {
            return $this->render('SatisfactoryOperationBundle:Operation:editEnrich.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));    
        } else if ($operation->getType() == 'Dedoublonner') {
            return $this->render('SatisfactoryOperationBundle:Operation:editDuplicate.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));
        } else if ($operation->getType() == 'Rejected') {
            return $this->render('SatisfactoryOperationBundle:Operation:editReject.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            )); 
        } else if ($operation->getType() == 'Rule') {
            return $this->render('SatisfactoryOperationBundle:Operation:editFilteringClient.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));      
        } else if ($operation->getType() == 'Publish') {
            return $this->render('SatisfactoryOperationBundle:Operation:editPublish.html.twig', array(
                        'operation' => $operation,
                        'edit_form' => $editForm->createView(),
            ));    
        } else if ($operation->getType() == 'Concat') {
            
            $em = $this->getDoctrine()->getManager();
            $concats = $em->getRepository('SatisfactoryOperationBundle:Concat')->findBy(array('operation' => $operation));
            $editFormConcats = array();
            
            foreach($concats as $concat){
              $contactForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationConcatenateType', $concat);  
              array_push($editFormConcats , $contactForm->createView());  
            }
            
            return $this->render('SatisfactoryOperationBundle:Operation:editConcat.html.twig', array(
                        'operation' => $operation,
                        'edit_form_concats' => $editFormConcats,
                        'edit_form' => $editForm->createView(),
                        'edit_form_operation' => $editFormOperation->createView(),
            ));
        } else if ($operation->getType() == 'Replacement') {
            
            $em = $this->getDoctrine()->getManager();
            $replacements = $em->getRepository('SatisfactoryOperationBundle:Replacement')->findBy(array('operation' => $operation));
            $editFormReplacements = array();
            
            foreach($replacements as $replace){
              $replaceForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationReplacementType', $replace);  
              array_push($editFormReplacements , $replaceForm->createView());  
            }
            
            return $this->render('SatisfactoryOperationBundle:Operation:editReplacement.html.twig', array(
                        'operation' => $operation,
                        'edit_form_replacements' => $editFormReplacements,
                        'edit_form' => $editForm->createView(),
                        'edit_form_operation' => $editFormOperation->createView(),
            ));
        }
    }
    
    /**
     * Edit Concat entity
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param entity Operation $operation
     * @param entity Concat $concat
     */
    public function editConcatAction(Request $request, Operation $operation, Concat $concat) 
    {
        $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationConcatenateType', $concat);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($concat);
        $em->flush();

        return $this->redirectToRoute('operation_operation_edit', array('id' => $operation->getId()));
    }
    
    /**
     * Edit Replacement entity
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param entity Operation $operation
     * @param entity Concat $replacement
     */
    public function editReplacementAction(Request $request, Operation $operation, Replacement $replacement) 
    {
        $editForm = $this->createForm('Satisfactory\OperationBundle\Form\OperationReplacementType', $replacement);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($replacement);
        $em->flush();

        return $this->redirectToRoute('operation_operation_edit', array('id' => $operation->getId()));
    }
    
    /**
     * Save json operation
     * 
     * @param integer $id (Id of operation)
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxUpdateOperationFilterAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();
        $operation = $em->getRepository('SatisfactoryOperationBundle:Operation')->find($id);
        
        if ($request->get('json') != "{}") {
            $operation->setJson($request->get('json'));
            $operation->setDescription($request->get('description'));
            $operation->setStatus($request->get('status'));
            $em->persist($operation);
            $em->flush();

            $response = new Response(json_encode($request->get('json')));
        }

         $response = new Response(json_encode(array('error' => 'Erreur')));

        return $response;
    }
    
    /**
     * Save manualcolumns operation
     * 
     * @param integer $id (Id of operation)
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function manualcolumnsAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();
        $operation = $em->getRepository('SatisfactoryOperationBundle:Operation')->find($id);
            $operation->setFilterManualColumns($request->get('json'));
            $em->persist($operation);
            $em->flush();

            $response = new Response(json_encode($request->get('json')));


        return $response;
    }

    /**
     * Returns sort operations by orderItem
     * 
     * @param integer $id (Id of dealing)
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxOrderOperationAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();

        $dealing = $em->getRepository('SatisfactoryOperationBundle:Dealing')->find($id);

        if (!$dealing) {
            throw $this->createNotFoundException('Unable to find dealing entity.');
        }

        //set operations order
        $tabOperations = explode(',', $request->get('sortingLog'));
        foreach ($tabOperations as $key => $id) {
            $operation = $em->getRepository('SatisfactoryOperationBundle:Operation')->find($id);
            
            if($operation->getType() == 'Rule')
                $operation->setOrderItem(++$key+1000);
            elseif($operation->getType() == 'Publish')
                $operation->setOrderItem(++$key+2000);
            elseif($operation->getType() == 'Archive')
                $operation->setOrderItem(++$key+3000);
            else
            $operation->setOrderItem(++$key);
            
            $em->persist($operation);
            $em->flush();
        }

        foreach ($dealing->getOperations() as $operation) {
            $array[] = array('id' => $operation->getId(),
                'order' => $operation->getOrderItem(),
                'text' => "[" . $operation->getType() . "]-[" . $operation->getDescription() . "] ",
                'type' => $operation->getType(),
                'status' => $operation->getStatus(),
                'url' => $this->generateUrl('operation_operation_edit', array('id' => $operation->getId())),
                'urlToDelete' => $this->generateUrl('operation_operation_delete', array('id' => $operation->getId())),
                
            );
        }

        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Delete operation
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request 
     * @param integer $id (Id of operation)
     */
    public function deleteAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();
        $operation = $em->getRepository('SatisfactoryOperationBundle:Operation')->find($id);

        if (!$operation) {
            throw $this->createNotFoundException('Unable to find Operation entity.');
        }

        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($operation);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add('notice', 'Enregistrement supprimé avec succès');

        return $this->redirectToRoute('operation_dealing_edit', array('id' => $operation->getDealing()->getId()));
    }
    
    /**
     * Activate operation.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Dealing entity $operation
     */
    public function activateAction(Request $request,  Operation $operation)
    {
        if (!$operation)
        {
            throw $this->createNotFoundException('Unable to find operation entity.');
        } 
        
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $operation->setStatus(!$operation->getStatus());
            $em->flush();
        }

        return $this->redirectToRoute('operation_dealing_edit', array('id'=> $operation->getDealing()->getId()));
    }
    
    /**
     * Delete concat
     * 
     * @param entity Operation $operation
     * @param entity Concat $concat
     */
    public function deleteConcatAction($operation, $concat) 
    {
        $em = $this->getDoctrine()->getManager();
        $concat = $em->getRepository('SatisfactoryOperationBundle:Concat')->find($concat);

        $em->remove($concat);
        $em->flush();

        return $this->redirectToRoute('operation_operation_edit', array('id' => $operation));
    }
    
    /**
     * Delete concat
     * 
     * @param entity Operation $operation
     * @param entity Concat $concat
     */
    public function deleteReplacementAction($operation, $replacement) 
    {
        $em = $this->getDoctrine()->getManager();
        $concat = $em->getRepository('SatisfactoryOperationBundle:Replacement')->find($replacement);

        $em->remove($concat);
        $em->flush();

        return $this->redirectToRoute('operation_operation_edit', array('id' => $operation));
    }

}
