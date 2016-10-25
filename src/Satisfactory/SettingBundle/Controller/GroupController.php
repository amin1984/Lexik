<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\Group;
use Satisfactory\SettingBundle\Form\GroupType;

/**
 * Controller Group Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class GroupController extends Controller
{
    /**
     * Creates a new Group entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('SatisfactorySettingBundle:Group')->findAll();
        $group = new Group();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\GroupType', $group);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($group->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\Group')->execute();
                $group->upload();
                // open csv 
                $path = $group->getPath();
                $row = 2;
                if (($handle = fopen($group->getUploadRootDir()."/".$group->getPath(), "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        for ($c = 0; $c < $num; $c++) {
                            $tab = explode(";", $data[$c]);
                            if ($tab[0] <> 'id_groupe') {
                                $group = new Group();
                                $group->setGroupId($tab[0]);
                                $group->setName($tab[1]);
                                $group->setPath($path);
                                $em->persist($group);
                                $em->flush();
                            }
                        }
                    }
                    fclose($handle);
                }

                $this->get('session')->getFlashBag()->add('notice', 'Chargement effectué avec succès.');
            }else{
                $this->get('session')->getFlashBag()->add('error', $group->checkFile());
            }
        }
        
        return $this->render('SatisfactorySettingBundle:Group:new.html.twig', array(
            'countGroup' => $groups ? count($groups): 0,
            'form' => $form->createView(),
            'path' => $groups ? $groups[0]->getPath(): null
        )); 
    }
}
