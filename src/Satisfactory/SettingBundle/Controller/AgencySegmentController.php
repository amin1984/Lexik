<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\AgencySegment;
use Satisfactory\SettingBundle\Form\AgencySegmentType;

/**
 * Controller AgencySegment Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class AgencySegmentController extends Controller
{
    /**
     * Creates a new AgencySegment entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $segments = $em->getRepository('SatisfactorySettingBundle:AgencySegment')->findAll();
        $segment = new AgencySegment();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\AgencySegmentType', $segment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($segment->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\AgencySegment')->execute();
                $segment->upload();
                // open csv 
                $path = $segment->getPath();
                $row = 2;
                if (($handle = fopen($segment->getUploadRootDir()."/".$segment->getPath(), "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        for ($c = 0; $c < $num; $c++) {
                            $tab = explode(";", $data[$c]);
                            if ($tab[0] <> 'id_agence') {
                                $segment = new AgencySegment();
                                $segment->setAgencyId($tab[0]);
                                $segment->setSegmentId($tab[1]);
                                $segment->setPath($path);
                                $em->persist($segment);
                                $em->flush();
                            }
                        }
                    }
                    fclose($handle);
                }

                $this->get('session')->getFlashBag()->add('notice', 'Chargement effectué avec succès.');
            }else{
                $this->get('session')->getFlashBag()->add('error', $segment->checkFile());
            }
        }
        
        return $this->render('SatisfactorySettingBundle:AgencySegment:new.html.twig', array(
            'countAgencySegment' => $segments ? count($segments): 0,
            'form' => $form->createView(),
            'path' => $segments ? $segments[0]->getPath(): null
        )); 
    }
}
