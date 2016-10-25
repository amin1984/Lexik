<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('type', 'choice', array(
                        'choices' => array('Filter' => 'Filtrer',
                            'Modify' => 'Modifier la structure',
                            'Enrich' => 'Enrichir',
                            'Dedoublonner' => 'Dédoublonner',
                            'Rejected' => 'Rejeter',
                            'Rule' => 'Règle de filtrage client',
                            'Table' => 'Table de correspondance',
                            'Concat' => 'Concaténer des fichiers sources',
                            //'Replacement' => 'Remplacement de caractères',
                            'Publish' => 'Publier',
                            'Archive' => 'Archiver',
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
            ->add('description')
                
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
