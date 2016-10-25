<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationDuplicateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('description')
                ->add('duplicateNameColumn')
                ->add('duplicateKeep', 'choice', array(
                    'choices' => array('1' => 'la 1ère instance trouvée',
                        '2' => 'la dernière instance trouvée'),
                    'expanded' => true,
                    'multiple' => false)
                )
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
