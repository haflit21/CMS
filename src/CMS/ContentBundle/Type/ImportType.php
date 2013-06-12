<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    	$lang_id = $options['lang_id'];

    	$builder
    		->add('fichier', 'file', array('label'=>'Fichier à importer'))
            ->add('contentType', 'entity', array(
            	'class' => 'CMSContentBundle:CMContentType',
            	'query_builder' => function(EntityRepository $er) {
            		return $er->getAllContentType();
            	},
            	'label' => 'Type de contenus',
            	'property' => 'title'
            	))
            ->add('category', 'entity', array(
                'class'=>'CMSContentBundle:CMCategory',
                'query_builder' => function(EntityRepository $er) use ($lang_id) {
                    return $er->getCategoryByLangIdQuery($lang_id);
                },
                'label'=>'Categories',
                'expanded'=>false,
                'required'=>true            
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null, 'lang_id' => '', 'om' => ''
        ));
    }

    public function getName()
    {
        return 'contentmanager_import';
    }
}
