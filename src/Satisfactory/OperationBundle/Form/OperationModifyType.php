<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationModifyType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('description')
                ->add('modifyStructure', 'choice', array(
                    'choices' => array('1' => 'Renommer',
                        '2' => 'Trier les lignes',
                        '3' => 'Ajouter colonne(s)',
                        '4' => 'Supprimer colonne(s)',
                        '5' => 'Réordonner une colonne',
                        '6' => 'Modifier le format d\'une colonne '
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('modifyNameColumnToRename')
                ->add('modifyNameColumnRename')
                ->add('modifyNameColumnToSort')
                ->add('modifyTypeSort', 'choice', array(
                    'choices' => array('1' => 'Alphabétique / croissant',
                        '2' => 'Alphabétique inversé / décroissant'
                     ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('modifyNameColumnToAdded')
                ->add('modifyAddedPosition', 'choice', array(
                    'choices' => array('1' => 'Ajouter en 1ère colonne',
                        '2' => 'Ajouter après la colonne'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('modifyNameColumnPosition')
                ->add('columnToDelete')
                ->add('nameColumnPosition')
                ->add('reorderPosition', 'choice', array(
                    'choices' => array('1' => 'Ajouter en 1ère colonne',
                        '2' => 'Ajouter après la colonne'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('reorderColumnName')
                ->add('nameColumnFormat')
                ->add('columnFormat', 'choice', array(
                    'choices' => array('1' => 'Format téléphone',
                                       '2' => 'Format date'
                    ),
                    'expanded' => false,
                    'multiple' => false)
                )
                ->add('targetFormatPhone', 'choice', array(
                    'choices' => array('1' => 'Transposer au format téléphonique international',
                                       '2' => 'Transposer dans un format manuel'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('countryCode')
                ->add('newFormat')
                ->add('sourceFormatDate')
                ->add('targetFormatDate' )
                ->add('status')
                

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Operation'
        ));
    }

}
