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
            ->add('menus_taxonomy','entity', array(
                'class' => 'CMSMenuBundle:MenuTaxonomy',
                'property' => 'name'
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
