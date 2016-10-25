<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnterCorrespondenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('input')
            //->add('output')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Document\EnterCorrespondence'
        ));
    }

    public function getName()
    {
        return 'satisfactory_operationbundle_entercorrespondencetype';
    }
}
