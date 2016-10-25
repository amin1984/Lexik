<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationPublishType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('description')
                ->add('publishProtocol', 'choice', array(
                    'choices' => array('ftp' => 'FTP',
                        'sftp' => 'SFTP'),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('publishHost')
                ->add('publishPort')
                ->add('publishLogin')
                ->add('publishPassword','password')
                ->add('publishDirectory')
                ->add('publishFileName')
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
