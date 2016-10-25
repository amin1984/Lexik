<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Satisfactory\OperationBundle\Entity\Filtering;
use Satisfactory\OperationBundle\Form\FilteringType;

/**
 * Filtering controller.
 *
 */
class FilteringController extends Controller
{
    /**
     * Lists all Filtering entities.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filterings = $em->getRepository('SatisfactoryOperationBundle:Filtering')->findAll();
        
        $clients = $em->getRepository('SatisfactoryUserBundle:User')->findByRole('ROLE_CLIENT');
        
        $client = 'all';
        
        $expression = "";
        
         if($request->getMethod() == 'POST'){
            
            if ($request->get('client') != 'all')
                $client = $em->getRepository('SatisfactoryUserBundle:User')->find($request->get('client'));

            $filterings = $em->getRepository('SatisfactoryOperationBundle:Filtering')->findBySearch($client, $request->get('expression'));
            
            $expression = $request->get('expression');
        }
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $filterings, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );

        return $this->render('SatisfactoryOperationBundle:Filtering:index.html.twig', array(
            'filterings' => $filterings,
            'clients' => $clients,
            'selectedClient' => $request->get('client'),
            'expression' => $expression,
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new Filtering entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request 
     */
    public function newAction(Request $request)
    {
        $filtering = new Filtering();
        $form = $this->createForm('Satisfactory\OperationBundle\Form\FilteringType', $filtering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //save operation data
            $date = new \DateTime();
            $filtering->setCreatedAt($date);
            $filtering->setCreatedBy($this->getUser());
            $filtering->setCreatorName($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($filtering);
            $em->flush();

            return $this->redirectToRoute('operation_filtering_index');
        }

       return $this->render('SatisfactoryOperationBundle:Filtering:new.html.twig', array(
            'filtering' => $filtering,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Filtering entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Filtering entity $filtering
     */
    public function editAction(Request $request, Filtering $filtering)
    {
        $editForm = $this->createForm('Satisfactory\OperationBundle\Form\FilteringType', $filtering);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //Save Filtering data
            $date = new \DateTime();
            $filtering->setUpdatedAt($date);
            $filtering->setUpdatedBy($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($filtering);
            $em->flush();

            return $this->redirectToRoute('operation_filtering_index');
        }

        return $this->render('SatisfactoryOperationBundle:Filtering:edit.html.twig', array(
            'filtering' => $filtering,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Filtering entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Filtering entity $filtering
     */
    public function deleteAction(Request $request, Filtering $filtering)
    {
        if (!$filtering)
        {
            throw $this->createNotFoundException('Unable to find Filtering entity.');
        } 
        
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($filtering);
            $em->flush();
        }

        return $this->redirectToRoute('operation_filtering_index');
    }
}
