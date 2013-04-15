<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label'=>'Title'))
            ->add('name', 'text', array('label'=>'Name'))
            ->add('published', 'choice', array(
                'choices'=> array('1'=>'Oui', '0'=>'Non'),
                'expanded'=>true,
                'multiple'=>false,
                'label'=>'Published'
            ))
            ->add('contentType', 'entity', array(
                'class'=>'CMSContentBundle:CMContentType',
                'label'=>'Types',
                'property'=>'title',
                'expanded'=>false,
                'multiple'=>true,
                'required'=>true
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMField'
        ));
    }

    public function getName()
    {
        return 'contentmanager_field';
    }
}
