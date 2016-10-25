<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Satisfactory\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OperationReplacementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('status')
                ->add('replaceName')
                ->add('replaceColumns')
                ->add('replaceUppercase')
                ->add('replaceLowercase')
                ->add('replaceCapitalize')
                ->add('replaceReplace')
                ->add('replaceStringToReplace')
                ->add('replaceStringToReplaceFormat', 'choice', array(
                    'choices' => array(
                        'string' => 'Chaîne exacte',
                        'wildcard' => 'Wildcard'),
                    //'data' => 0,
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('replaceReplacement')
                ->add('replaceReplacementFormat', 'choice', array(
                    'choices' => array(
                        'full' => 'Remplacement complet de la cellule',
                        'part' => 'Remplacement uniquement de la partie qui correspond à la "chaîne à remplacer" qand il est trouvé dans la cellule'),
                    //'data' => 0,
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
            'data_class' => 'Satisfactory\OperationBundle\Entity\Replacement'
        ));
    }
}
