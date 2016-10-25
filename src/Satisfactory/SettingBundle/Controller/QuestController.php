<?php

namespace Satisfactory\SettingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Satisfactory\SettingBundle\Entity\Quest;
use Satisfactory\SettingBundle\Form\QuestType;

/**
 * Controller Quest Controller
 *
 * @author Arous Amin <amin@celaneo.com>
 */

class QuestController extends Controller
{
    /**
     * Creates a new Quest entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $quests = $em->getRepository('SatisfactorySettingBundle:Quest')->findAll();
        $quest = new Quest();
        $form = $this->createForm('Satisfactory\SettingBundle\Form\QuestType', $quest);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($quest->checkFile() == NULL) {
                $em->createQuery('DELETE FROM Satisfactory\SettingBundle\Entity\Quest')->execute();
                $quest->upload();
                // open csv 
                $path = $quest->getPath();
                $row = 2;
                if (($handle = fopen($quest->getUploadRootDir()."/".$quest->getPath(), "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        for ($c = 0; $c < $num; $c++) {
                            $tab = explode(";", $data[$c]);
                            if ($tab[0] <> 'id_typeenquete') {
                                $quest = new Quest();
                                $quest->setQuestId($tab[0]);
                                $quest->setName($tab[1]);
                                $quest->setPath($path);
                                $em->persist($quest);
                                $em->flush();
                            }
                        }
                    }
                    fclose($handle);
                }

                $this->get('session')->getFlashBag()->add('notice', 'Chargement effectué avec succès.');
            }else{
                $this->get('session')->getFlashBag()->add('error', $quest->checkFile());
            }
        }
        
        return $this->render('SatisfactorySettingBundle:Quest:new.html.twig', array(
            'countQuest' => $quests ? count($quests): 0,
            'form' => $form->createView(),
            'path' => $quests ? $quests[0]->getPath(): null
        )); 
    }
}
