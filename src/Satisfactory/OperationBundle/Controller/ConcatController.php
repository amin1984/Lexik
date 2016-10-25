<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\OperationBundle\Entity\Concat;
use Satisfactory\OperationBundle\Form\ConcatType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Controller Concat Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */
class ConcatController extends Controller 
{

    /**
     * Creates a new Concat entity.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request, $id) 
    {
        $em = $this->getDoctrine()->getManager();
        $concat = new Concat();
        
        $form = $this->createForm('Satisfactory\OperationBundle\Form\ConcatType', $concat);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            $em->persist($concat);

            $em->flush();

            return $this->redirectToRoute('operation_concat_edit', array('id' => $operation->getId()));
        }
        
        return $this->render('SatisfactoryOperationBundle:Operation:new.html.twig', array(
                    'dealing' => $dealing,
        ));
    }

}
