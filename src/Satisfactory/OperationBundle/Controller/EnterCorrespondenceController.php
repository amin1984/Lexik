<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\OperationBundle\Document\EnterCorrespondence;
use Satisfactory\OperationBundle\Form\EnterCorrespondenceType;
use Satisfactory\OperationBundle\Document\Correspondence;

/**
 * Controller EnterCorrespondence Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class EnterCorrespondenceController extends Controller
{
    /**
     * Creates a new EnterCorrespondence entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    
    public function newAction(Request $request)
    {
        $document = new EnterCorrespondence();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\EnterCorrespondenceType', $document);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $correspondence = $em->getRepository('SatisfactoryOperationBundle:Correspondance')->find($request->get('id'));
         
        //Columns
        $columns = array();
        foreach ($correspondence->getColumns()as $key => $value) {
            $columns[$key] = $value;
        }
        //Input
        $input = array();
        foreach ($correspondence->getColumns()as $key => $value) {
            if($value != end($columns))
            $input[$key] = $value;
        }
        //Output
        $output = array();
        foreach ($correspondence->getColumns()as $key => $value) {
            if($value == end($columns))
            $output[$key] = $value;
        }

        if ($form->isSubmitted()) {
            
            //Save Input in Correspondance entity
            $documentInput = array(); 
            foreach ($input as $value) 
            {
                $documentInput[$value] =  $request->get($value);
            }
            $document->setInput($documentInput);
            //Save Output in Correspondance entity
            $documentOutput = array(); 
            foreach ($output as $value) 
            {
                $documentOutput[$value] =  $request->get($value);
            }
            $document->setOutput($documentOutput);
            //Save correspondance data in mongoDb
            $date = new \DateTime();
            $document->setCorrespondenceId($request->get('id'));
            $document->setCreatedAt($date);
            $document->setCreatedBy($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            $dm = $this->get('doctrine_mongodb')->getManager();
            //Persist and flush mongoDb
            $dm->persist($document);
            $dm->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Enregistrement effectué avec succès');

            return $this->redirectToRoute('operation_correspondance_edit', array('id' => $request->get('id')));
        }
        
        return $this->render('SatisfactoryOperationBundle:EnterCorrespondence:new.html.twig', array(
            'document' => $document,
            'output'    => $output,
            'input'    => $input,
            'form'     => $form->createView(),
            'correspondenceId' => $request->get('id')
        ));
    }

    /**
     * Update a new EnterCorrespondence entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    
    public function editAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $document = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->find($id);
        
        $form = $this->createForm('Satisfactory\OperationBundle\Form\EnterCorrespondenceType', $document);
        $form->handleRequest($request);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find EnterCorrespondence entity.');
        }

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $input = array(); 
            foreach ($document->getInput()as $key => $value) 
            {
                $input[$key] =  $request->get($key);
            }
            $document->setInput($input);
            $output = array(); 
            foreach ($document->getOutput()as $key => $value) 
            {
                $output[$key] =  $request->get($key);
            }
            $document->setOutput($output);
            $dm->persist($document);
            $dm->flush();
            
            return $this->redirect($this->generateUrl('operation_correspondance_edit', array('id' => $document->getCorrespondenceId())));
        }
        
        $correspondence = $dm->getRepository('SatisfactoryOperationBundle:Correspondence')->findByIndex($document->getCorrespondenceId());

        return $this->render('SatisfactoryOperationBundle:EnterCorrespondence:edit.html.twig', array(
            'document'    => $document,
            'form'   => $form->createView(),
            'correspondence'    => $correspondence,
        ));
    }
    
    /**
     * Delete a new EnterCorrespondence entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    
    public function deleteAction(Request $request)
    {
         $dm = $this->get('doctrine_mongodb')->getManager();
         $document = $dm->getRepository('SatisfactoryOperationBundle:EnterCorrespondence')->find($request->get('id'));
         $correspondenceId = $document->getCorrespondenceId();
         
        if ($request->getMethod() == 'GET') {
            
            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('operation_correspondance_edit', array('id' => $correspondenceId)));
    }
}
