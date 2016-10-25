<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Satisfactory\UserBundle\Repository\UserRepository;

class FilteringType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sharingData')
            ->add('columnName')
            ->add('value')
            ->add('type', 'choice', array(
                    'choices' => array('1' => 'ne conserver que les lignes avec cette valeur',
                        '2' => 'exclure les lignes avec cette valeur'
                        ),
                    'data' => '1',    
                    'expanded' => true,
                    'multiple' => false)
                )
            ->add('dateStart','datetime',array('widget' => 'single_text',
                                                'input' => 'datetime',
                                                'format' => 'dd/MM/yyyy',
                                                )
                  )
            ->add('dateEnd','datetime',array('widget' => 'single_text',
                                                'input' => 'datetime',
                                                'format' => 'dd/MM/yyyy',
                                                )
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
             ->add('isActive')               
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Filtering'
        ));
    }
}
