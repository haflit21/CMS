<?php

namespace CMS\BlocBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class BlocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('title')
            ->add('position',
                'choice', array( 'empty_value' => 'Choose a position', 'choices' =>
                    array(
                            'top' => 'Haut',
                            'menu_top'=>'Menu haut',
                            'banner' => 'Bannière',
                            'bottom'=>'Bas',
                            'left'=>'Gauche',
                            'right'=>'Droite',
                            'admin_menu_top' => 'Admin - menu haut',
                            'admin_menu_left' => 'Admin - menu gauche',
                            'admin_breadcrumb' => 'Admin - Breadcrumb',

                    ),
                'label' => 'Position'
                ))
            ->add('published', 'publish', array('label' => 'Publié')
                )
            ->add('all_published', 'choice', array(
                'choices' => array(
                    '1' => 'Oui',
                    '0' => 'Non'
                    ),
                'attr' => array(
                    'class' => 'all_published publish_field'
                    ),
                'expanded' => true,
                'multiple' => false,
                'label' => 'Toutes les pages'
                ))
            ->add('categories', 'entity', array(
                'class' => 'CMSContentBundle:CMCategory',
                'query_builder' =>
                    function(EntityRepository $er) use ($lang_id) {
                        return $er->getCategoryByLangIdQuery($lang_id);
                    },
                'required' => false,
                'multiple' => true,
                'label' => 'Catégorie',
                'attr' => array('class' => 'selectable'),
                'property'=>'title'
                ))
            ->add('contents', 'entity', array(
                'class' => 'CMSContentBundle:CMContent',
                'query_builder' =>
                    function(EntityRepository $er) use ($lang_id) {
                        return $er->getContentByLangIdQuery($lang_id);
                    },
                'required' => false,
                'multiple' => true,
                'label' => 'Contenu',
                'attr' => array('class' => 'selectable'),
                'property' => 'title',
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\BlocBundle\Entity\Bloc', 'lang_id' => ''
        ));
    }

    public function getName()
    {
        return 'bloc';
    }
}
