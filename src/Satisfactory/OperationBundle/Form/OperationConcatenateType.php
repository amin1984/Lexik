<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Satisfactory\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OperationConcatenateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('status')
                ->add('concatProtocol', 'choice', array(
                    'choices' => array('ftp' => 'FTP',
                        'sftp' => 'SFTP'),
                    //'data' => 0,
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('concatHost','text',array('required' => true))
                ->add('concatPort','text')
                ->add('concatLogin','text')
                ->add('concatPassword', 'password')
                ->add('concatDirectory','text')
                ->add('concatFilename','text')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Concat'
        ));
    }
}
