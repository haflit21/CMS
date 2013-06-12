<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $em = $options['om'];
        $builder
            ->add('title', 'text', array('label'=>'Title'))
            ->add('tags', 'tag_selector')
            ->add('published', 'choice', array(
                'choices'=> array('1'=>'Oui', '0'=>'Non'),
                'expanded'=>true,
                'multiple'=>false,
                'label'=>'Published'
            ))
            ->add('description', 'ckeditor', array('label'=>'Description', 'required'=>false))
            ->add('categories', 'entity', array(
                'class'=>'CMSContentBundle:CMCategory',
                'query_builder' => function(EntityRepository $er) use ($lang_id) {
                    return $er->getCategoryByLangIdQuery($lang_id);
                },
                'label'=>'Categories',
                'expanded'=>false,
                'multiple'=>true,
                'required'=>true,
                'attr' => array('class' => 'categories')
            ))
            ->add('url', 'text', array('label'=>'Url', 'required' => false))    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMContent', 'lang_id' => '', 'om' => ''
        ));
    }

    public function getName()
    {
        return 'contentmanager_content';
    }
}
