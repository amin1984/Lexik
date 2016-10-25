<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Satisfactory\UserBundle\Repository\UserRepository;

class RejectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                    'choices' => array('Sollicitation' => 'Sollicitation',
                        'Blackliste' => 'Blackliste',
                        'Quota' => 'Quota'),
                    'expanded' => true,
                    'multiple' => false)
                ) 
            ->add('client', 'entity', array(
                    'label' => 'Client',
                    'class' => 'Satisfactory\\UserBundle\\Entity\\User',
                    'property' => 'userName',
                    'query_builder' => function(UserRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->where('u.roles LIKE :roles')
                                ->setParameter('roles', '%"ROLE_CLIENT"%');
                    },
                    
                ))
            ->add('ruleName')                
            ->add('columnName')
            ->add('maxProcess')
            ->add('processBy', 'choice', array(
                    'choices' => array('1' => 'jour',
                        '2' => 'semaine',
                        '3' => 'mois'),
                    'expanded' => true,
                    'multiple' => false)
                )  
            ->add('processDay')
            ->add('processWeek')
            ->add('processMonth')
            ->add('file','file')    
            ->add('numberMaxToSend')
            ->add('periodOfSend')
            ->add('typeSendQuota', 'choice', array(
                    'choices' => array('1' => 'jours',
                        '2' => 'semaine',
                        '3' => 'mois'),
                    'expanded' => true,
                    'multiple' => false)
                )     
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Reject'
        ));
    }
}
