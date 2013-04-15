<?php

namespace CMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name','text',array('label' => "Nom du role"))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'CMS\AdminBundle\Entity\Role');
    }

    public function getName()
    {
        return 'role';
    }
}
