<?php

namespace CMS\SitemapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SitemapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('display_title_menu', 'choice', array('choices' => array(0 => 'Non', 1 => 'Oui'), 'expanded' => true))
            ->add('class_columns', 'text', array('label' => 'Classe css appliquée au plan du site'))
            ->add('menus_taxonomy','entity', array(
                'class' => 'CMSMenuBundle:MenuTaxonomy',
                'property' => 'name',
                'multiple' => 'multiple'
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\SitemapBundle\Entity\Sitemap'
        ));
    }

    public function getName()
    {
        return 'cms_sitemapbundle_sitemaptype';
    }
}
