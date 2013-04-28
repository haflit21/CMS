<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('title', 'text', array('label'=>'Title'))
             ->add('published', 'choice', array(
                'choices'=> array('1'=>'Oui', '0'=>'Non'),
                'expanded'=>true,
                'multiple'=>false,
                'label'=>'Published'
            ))
            ->add('description', 'ckeditor', array(
                'label'     => 'Description',
                'attr'      => array('class'=>'ckeditor'),
                'required'   => false
            ))
            ->add('parent', 'entity', array(
                    'class' => 'CMSContentBundle:CMCategory',
                    'query_builder' => function(EntityRepository $er) use ($lang_id) {
                        return $er->getCategoryByLangIdQuery($lang_id);
                    },
                    'empty_value' => 'Choisissez une catégorie parente',
                    'required' => false,
                ))
            ->add('ordre', 'entity', array(
                    'class' => 'CMSContentBundle:CMCategory',
                    'query_builder' => function(EntityRepository $er) use ($lang_id) {
                        return $er->getCategoryByLangIdQuery($lang_id);
                    },
                    'empty_value' => 'Après l\'élément',
                    'required' => false,
                ))
            ->add('metatitle', 'text', array('label'=>'MetaTitle'))
            ->add('metadescription', 'text', array('label'=>'MetaDescription', 'required'=>false))
            ->add('canonical', 'text', array('label'=>'Canonical', 'required'=>false))
            ->add('url', 'text', array('label'=>'Url', 'required' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMCategory', 'lang_id' => ''
        ));
    }

    public function getName()
    {
        return 'contentmanager_category';
    }
}
