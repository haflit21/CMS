<?php

namespace CMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username','text',array('label' => "Nom d'utilisateur"))
            ->add('firstname','text', array('label' => 'Prénom'))
            ->add('lastname', 'text', array('label' => 'Nom'))
            ->add('password','repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('label' => 'Mot de passe')
                )
            )
            ->add('email', 'email', array('label' => 'Adresse e-mail'))
            ->add('language', 'choice', array(
                    'choices'   => array('fr' => 'Français', 'en' => 'English', 'de' => 'Deutsch'),
                    'label' => 'Langue',
                    )
                )
            ->add('groups', 'entity', array(
                'class' => 'CMSAdminBundle:Role',
                'empty_value' => 'Choisissez un groupe',
                'property' => 'name',
                'multiple' => true,
                'label' => 'Groupe',
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'CMS\AdminBundle\Entity\User');
    }

    public function getName()
    {
        return 'user';
    }
}
