<?php

namespace Satisfactory\UserBundle\Controller;

use Satisfactory\UserBundle\Entity\User;
use Satisfactory\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller User Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request 
     */
    public function indexAction(Request $request)
    {
        return $this->render('SatisfactoryUserBundle:User:index.html.twig');
    }
    
    /**
     * Lists of all  User entities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxlistAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $order = $request->get('order') ? $request->get('order') : 'updatedAt';
        
        $numPage = $request->get('page') ? $request->get('page') : 1;
        
        $orderType = $request->get('orderType') ? $request->get('orderType') : 'DESC';
        
        $input = $request->get('input') ? $request->get('input') : null;
        
        $users = $em->getRepository('SatisfactoryUserBundle:User')->paginatorQuery($limit=10, $offset=(($numPage-1)*$limit), $input, $order, $orderType);
        
        $usersLimitOff = $em->getRepository('SatisfactoryUserBundle:User')->paginatorQueryLimitOff( (($numPage-1)*$limit), $input, $order, $orderType);
        
        $countUsers = count($usersLimitOff);
        
        // ondances
        $array = array();
        foreach ($users as $key=>$value){
            $array[$key]['id'] =  $value->getId();
            $array[$key]['username'] =  $value->getUserName();
            $array[$key]['creatorName'] =  $value->getCreatorName();
            // updatedAt
            if ($value->getUpdatedAt())
                $array[$key]['updatedAt'] = $value->getUpdatedAt()->format('d-m-Y H:i:s');
            else
                $array[$key]['updatedAt'] = null;
            
            $array[$key]['pathEdit'] =  $this->generateUrl('user_edit',array('id' => $value->getId()));
            $array[$key]['pathDelete'] =  $this->generateUrl('user_delete',array('id' => $value->getId()));
            
            $dealing =  $em->getRepository('SatisfactoryOperationBundle:Dealing')->findBy(array('client' => $value->getId()));
            // Dealings list
            $arrayDealings = array();
            foreach ($dealing as $k=>$dlg){ 
              $arrayDealings[$k]['id'] = $dlg->getId();
              $arrayDealings[$k]['name'] = $dlg->getName();
              $arrayDealings[$k]['pathDealing'] =  $this->generateUrl('operation_dealing_edit',array('id' => $dlg->getId()));
            }
            $array[$key]['dealings'] = $arrayDealings;
            
        }
        
        return new Response(json_encode(array(
                    'users' => $array,
                    'pages' => ceil($countUsers / $limit) ,
                    'currentPage' => $request->get('page')>=1 ? (($request->get('page')-1)*$limit)+1 : 1,
                    'limit' => $limit,
                    'offset' => $offset,
                )
        ));
        
    }

    /**
     * Creates a new User entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request 
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('Satisfactory\UserBundle\Form\UserType', $user);
        $form->handleRequest($request);
        $user->setCreatedBy($this->getUser());
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->addRole('ROLE_CLIENT');
            $user->setCreatorName($this->getUser()->getLastName()." ".$this->getUser()->getFirstName());
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('SatisfactoryUserBundle:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @param entity User $user
     * @param \Symfony\Component\HttpFoundation\Request $request 
     */
    public function editAction(Request $request, User $user)
    {
        if (!is_object($user) || !$user instanceof UserInterface)
        {
            throw new AuthenticationException();
        }
        $editForm = $this->createForm('Satisfactory\UserBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //updateAt and updateBy
            $date = new \DateTime('now');
            $user->setUpdatedAt($date);
            $user->setUpdatedBy($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('user_index');
        }

        return $this->render('SatisfactoryUserBundle:User:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @param entity User $user
     * @param \Symfony\Component\HttpFoundation\Request $request 
     */
    public function deleteAction(Request $request, User $user)
    {
       if (!is_object($user) || !$user instanceof UserInterface)
        {
            throw new AuthenticationException();
        }
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('user_index');
    }

}
