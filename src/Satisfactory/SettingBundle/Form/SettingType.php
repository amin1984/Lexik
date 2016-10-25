<?php

namespace Satisfactory\SettingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name')
                ->add('agency', 'entity', array(
                    'class' => 'SatisfactorySettingBundle:Agency',
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->add('quest', 'entity', array(
                    'class' => 'SatisfactorySettingBundle:Quest',
                    'expanded' => true,
                    'multiple' => false
                ))
                ->add('segment', 'entity', array(
                    'class' => 'SatisfactorySettingBundle:Segment',
                    'expanded' => true,
                    'multiple' => true
                ))
                ->add('dateBegin', 'datetime', array('widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                        )
                )
                ->add('dateEnd', 'datetime', array('widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                        )
                )

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\SettingBundle\Entity\Setting'
        ));
    }

}
