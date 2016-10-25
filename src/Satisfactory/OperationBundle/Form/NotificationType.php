<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('email', 'email', array('label' => ' ',
                                          'attr' => array('class' => 'form-control ' )
                                         )
                 )
            ->add('type', 'choice', array(
                    'label' => 'Recevoir :',
                    'attr' => array('class' => '' ),
                    'choices' => array(
                                  'error' => 'les notifications d\'erreur', 
                                  'execute' => 'les notifications pour bonne exécution'),
                    'expanded' => true,
                    'multiple' => true)
                )   
       ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Notification'
        ));
    }

}
