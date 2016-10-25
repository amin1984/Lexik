<?php

namespace Satisfactory\OperationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Satisfactory\OperationBundle\Form\NotificationType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Satisfactory\UserBundle\Repository\UserRepository;

class DealingType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name')
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
                ->add('protocol', 'choice', array(
                    'choices' => array('ftp' => 'FTP',
                        'sftp' => 'SFTP'),
                        'expanded' => true,
                        'multiple' => false)
                )
                ->add('host')
                ->add('port')
                ->add('login')
                ->add('password', 'password')
                ->add('directory')
                ->add('fileName')
                ->add('sepa', 'choice', array(
                    'choices' => array("    " => 'tabulation',
                        ';' => ';',
                        ',' => ',',
                        '|' => '|',
                        'autre' => 'autre'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('other')
                ->add('quotation', 'choice', array(
                    'choices' => array('oui' => 'Oui',
                        'non' => 'Non'
                    ),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('encoding', 'choice', array(
                    'choices' => array('ANSI' => 'ANSI',
                        'UTF8' => 'UTF8',
                        'UTF8-sans BOM' => 'UTF8-sans BOM'
                    ),
                    'expanded' => false,
                    'multiple' => false)
                )
                ->add('nbLigneToDelete', 'choice', array(
                    'choices' => array(
                        0 => 0,
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                        7 => 7,
                        8 => 8,
                        9 => 9,
                        10 => 10,
                        11 => 11,
                        12 => 12,
                        13 => 13,
                        14 => 14,
                        15 => 15,
                        16 => 16,
                        17 => 17,
                        18 => 18,
                        19 => 19,
                        20 => 20,
                        21 => 21,
                        22 => 22,
                        23 => 23,
                        24 => 24,
                        25 => 25,
                        26 => 26,
                        27 => 27,
                        28 => 28,
                        29 => 29,
                        30 => 30,
                        31 => 31,
                        32 => 32,
                        33 => 33,
                        34 => 34,
                        35 => 35,
                        36 => 36,
                        37 => 37,
                        38 => 38,
                        39 => 39,
                        40 => 40,
                        41 => 41,
                        10 => 42,
                        43 => 43,
                        44 => 44,
                        45 => 45,
                        46 => 46,
                        47 => 47,
                        48 => 48,
                        49 => 49,
                        50 => 50,
                    ),
                    'expanded' => false,
                    'multiple' => false)
                )
                ->add('notifications', 'collection', array(
                    'type' => new NotificationType(),
                    'prototype' => true,
                    'allow_add' => true,
                    'label' => false,
                ))
                ->add('recurence', 'choice', array(
                    'choices' => array(
                        '0' => 'Récurrence désactivée (pas de programmation périodique pour ce traitement)',
                        '1' => 'Quotidienne / hebdomadaire',
                        '2' => 'Mensuel',
                        '3' => 'Dès le dépôt du fichier (sous 5 minutes)'),
                    'expanded' => true,
                    'multiple' => false)
                )
                ->add('days', 'choice', array(
                    'choices' => array('1' => 'lundi',
                        '2' => 'mardi',
                        '3' => 'mercredi',
                        '4' => 'jeudi',
                        '5' => 'vendredi',
                        '6' => 'samedi',
                        '7' => 'dimanche',
                    ),
                    'expanded' => true,
                    'multiple' => true)
                )
                ->add('timeDay', 'time', array(
                    'minutes' => range(0, 55, 5)
                ))
                ->add('dayOfMonth', 'text')
                ->add('timeMonth', 'time', array(
                    'minutes' => range(0, 55, 5)
                ))
                ->add('isFileHeader')
                ->add('fileHeader', 'textarea', array(
                    'attr' => array('cols' => '5', 'rows' => '5'),
                    'data' => 'colonne_1;colonne_2;colonne_3;colonne_4;colonne_5')
                )
                ->add('deleteSemicolon')
                ->add('isCompressed', 'choice', array(
                    'choices' => array(0 => 'non',
                        1 => 'oui'
                    ),
                    'choice_attr' => array(
                        0 => array(
                            'value' => 0,
                            'ng-model' => 'type',
                            'ng-change' => 'typeValue(type)'),
                        1 => array(
                            'value' => 1,
                            'ng-model' => 'type',
                            'ng-change' => 'typeValue(type)'),
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'data' => 0
                        )
                )
                ->add('compressionFormat', 'choice', array(
                    'choices' => array('zip' => 'zip',
                        'rar' => 'rar',
                        'gz' => 'gz'
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => false,)
                )
                ->add('compressionFile')
                ->add('isFileNameWildcard', 'choice', array(
                    'choices' => array(0 => 'Nom ferme',
                        1 => 'Wildcard'
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'data' => 0
                        )
                )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Satisfactory\OperationBundle\Entity\Dealing'
        ));
    }

}
