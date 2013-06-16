<?php

namespace CMS\MenuBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $menu_taxonomy = $options['menu_taxonomy'];
        $builder
            ->add('title')
            ->add('isRoot', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'is root ?', 'data' => 0))
            ->add('published', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'Publié', 'data' => 1))
            ->add('default_page', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'Page par défaut', 'data' => 0))
            ->add('intern', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'Lien interne'))
            ->add('name_route', 'text', array('label' => 'Nom de la route', 'required' => false))
            ->add('category', 'entity', array(
                'class'=>'CMSContentBundle:CMCategory',
                'query_builder' => function(EntityRepository $er) use ($lang_id) {
                    return $er->getCategoryByLangIdQuery($lang_id);
                },
                'label'=>'Catégories',
                'property'=>'title',
                'required'=>false
            ))
            ->add('content', 'entity', array(
                'class'=>'CMSContentBundle:CMContent',
                'query_builder' => function(EntityRepository $er) use ($lang_id) {
                    return $er->getContentByLangIdQuery($lang_id);
                },
                'label'=>'Contenus',
                'property'=>'title',
                'required'=>false
            ))
            ->add('parent', 'entity', array(
                    'class' => 'CMSMenuBundle:Menu',
                    'query_builder' => function(EntityRepository $er) use ($menu_taxonomy) {
                        return $er->getMenuParent($menu_taxonomy);
                    },
                    'empty_value' => 'Choisissez un menu parent',
                    'required' => false,
                ))
            ->add('ordre', 'entity', array(
                    'class' => 'CMSMenuBundle:Menu',
                    'query_builder' => function(EntityRepository $er) use ($menu_taxonomy) {
                        return $er->getMenuParent($menu_taxonomy);
                    },
                    'empty_value' => 'Après l\'élément',
                    'required' => false,
                ))
            ->add('id_menu_taxonomy', 'entity', array(
                        'class' => 'CMSMenuBundle:MenuTaxonomy',
                        'empty_value' => false,
                        'preferred_choices' => array($menu_taxonomy),
                        'property' => 'name',
                        'read_only' => true,
                        'label' => ' '
                    )
                )
            ->add('classIcon', 'text', array(
                    'label' => 'Classe de l\'icône',
                    'required' => false
                    )
                )
            ->add('displayIcon', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'Afficher l\'icône'))
            ->add('displayName', 'choice', array('choices' => array(1 => 'Oui', 0 => 'Non'), 'expanded' => true, 'label' => 'Afficher le titre'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\MenuBundle\Entity\Menu', 'lang_id' => '', 'menu_taxonomy' => ''
        ));
    }

    public function getName()
    {
        return 'menu';
    }
}
