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
        $fields = $options['fields'];
        //var_dump($fields); die;

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
            ->add('ordre_classement', 'choice', array('label' => 'Champ pour classer la catégorie', 'choices' => $fields))
            ->add('direction_classement', 'choice', array('label' => 'Direction du classement', 'choices' => array('ASC' => 'croissant', 'DESC' => 'décroissant')))
            ->add('url', 'text', array('label'=>'Url', 'required' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMCategory', 'lang_id' => '', 'fields' => array()
        ));
    }

    public function getName()
    {
        return 'contentmanager_category';
    }
}
