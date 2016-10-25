<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\AgencyQuest;
use Satisfactory\SettingBundle\Form\AgencyQuestType;

/**
 * Controller AgencyQuest Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class AgencyQuestController extends Controller
{
    /**
     * Creates a new AgencyQuest entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $segments = $em->getRepository('SatisfactorySettingBundle:AgencyQuest')->findAll();
        $segment = new AgencyQuest();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\AgencyQuestType', $segment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($segment->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\AgencyQuest')->execute();
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
                                $segment = new AgencyQuest();
                                $segment->setAgencyId($tab[0]);
                                $segment->setQuestId($tab[1]);
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
        
        return $this->render('SatisfactorySettingBundle:AgencyQuest:new.html.twig', array(
            'countAgencyQuest' => $segments ? count($segments): 0,
            'form' => $form->createView(),
            'path' => $segments ? $segments[0]->getPath(): null
        )); 
    }
}
