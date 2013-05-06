<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
            ->add('type', 'text', array('label' => 'Titre'))
            ->add('name', 'text', array('label' => 'Nom'))
            ->add('published', 'choice', array(
                'choices'=> array('1'=>'Oui', '0'=>'Non'),
                'expanded'=>true,
                'multiple'=>false,
                'label'=>'Published'
            ))
            ->add('value', 'text', array('label' => 'Value'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMMeta'
        ));
    }

    public function getName()
    {
        return 'contentmanager_meta';
    }
}