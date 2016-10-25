<?php

namespace Satisfactory\OperationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Satisfactory\OperationBundle\Entity\Notification;
use Satisfactory\OperationBundle\Form\NotificationType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller Notification Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class NotificationController extends Controller
{
   /**
     * Deletes a Notification entity.
     *
     */
    public function deleteAction(Request $request, Notification $notification)
    {
        $id = $notification->getId();
        if ($request->getMethod() == 'GET') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($notification);
            $em->flush();
        }
        
        $response = new Response(json_encode(array('id' => $id)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
