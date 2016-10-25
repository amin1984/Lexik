<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\Agency;
use Satisfactory\SettingBundle\Form\AgencyType;

/**
 * Controller Agency Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class AgencyController extends Controller
{
    /**
     * Creates a new Agency entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agencys = $em->getRepository('SatisfactorySettingBundle:Agency')->findAll();
        $agency = new Agency();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\AgencyType', $agency);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($agency->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\Agency')->execute();
                $agency->upload();
                // open csv 
                $path = $agency->getPath();
                $row = 2;
                if (($handle = fopen($agency->getUploadRootDir()."/".$agency->getPath(), "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        for ($c = 0; $c < $num; $c++) {
                            $tab = explode(";", $data[$c]);
                            if ($tab[0] <> 'id_agence') {
                                $agency = new Agency();
                                $agency->setAgencyId($tab[0]);
                                $agency->setName($tab[1]);
                                $agency->setPath($path);
                                $em->persist($agency);
                                $em->flush();
                            }
                        }
                    }
                    fclose($handle);
                }

                $this->get('session')->getFlashBag()->add('notice', 'Chargement effectué avec succès.');
            }else{
                $this->get('session')->getFlashBag()->add('error', $agency->checkFile());
            }
        }
        
        return $this->render('SatisfactorySettingBundle:Agency:new.html.twig', array(
            'countAgency' => $agencys ? count($agencys): 0,
            'form' => $form->createView(),
            'path' => $agencys ? $agencys[0]->getPath(): null
        )); 
    }
}
