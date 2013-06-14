<?php

namespace CMS\MenuBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuTaxonomyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('alias')
            ->add('is_menu_admin','choice',array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true))
        ;
    }

    public function getName()
    {
        return 'menu_taxonomy';
    }
}
