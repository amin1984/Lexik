<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationEnrichType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('description')
                ->add('enrichFilter', 'choice', array(
                    'choices' => array('1' => 'Appliquer une correspondance de valeurs sur une colonne',
                        '2' => 'Appliquer une règle d\'agrégation textuelle'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('enrichColumnName')
                ->add('correspondance', 'entity', array(
                    'label' => 'Correspondance',
                    'class' => 'Satisfactory\\OperationBundle\\Entity\\Correspondance',
                    'property' => 'Name'
                ))
                ->add('enrichColumnNameRuleSource')
                ->add('enrichRule')
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
