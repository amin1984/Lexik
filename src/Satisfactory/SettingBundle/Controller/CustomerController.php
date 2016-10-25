<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\Customer;
use Satisfactory\SettingBundle\Form\CustomerType;

/**
 * Controller Customer Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class CustomerController extends Controller
{
    public function indexAction()
    {
        return $this->render('SatisfactorySettingBundle:Customer:index.html.twig');
    }
    
    /**
     * Creates a new Customer entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $customers = $em->getRepository('SatisfactorySettingBundle:Customer')->findAll();
        $customer = new Customer();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\CustomerType', $customer);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($customer->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\Customer')->execute();
                $customer->upload();
                // open csv 
                $path = $customer->getPath();
                $row = 2;
                if (($handle = fopen($customer->getUploadRootDir()."/".$customer->getPath(), "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        for ($c = 0; $c < $num; $c++) {
                            $tab = explode(";", $data[$c]);
                            if ($tab[0] <> 'id_utilisateur') {
                                $customer = new Customer();
                                $customer->setUser($tab[0]);
                                $customer->setName($tab[1]);
                                $customer->setPath($path);
                                $em->persist($customer);
                                $em->flush();
                            }
                        }
                    }
                    fclose($handle);
                }

                $this->get('session')->getFlashBag()->add('notice', 'Chargement effectué avec succès.');
            }else{
                $this->get('session')->getFlashBag()->add('error', $customer->checkFile());
            }
        }
        
        return $this->render('SatisfactorySettingBundle:Customer:new.html.twig', array(
            'countCustomer' => $customers ? count($customers) : 0,
            'form' => $form->createView(),
            'path' => $customers ? $customers[0]->getPath(): null
        ));
    }
}
