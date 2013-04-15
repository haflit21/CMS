<?php
namespace CMS\BlocBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CMS\BlocBundle\Type\BlocType;

class BlocBreadcrumbType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('bloc', new BlocType(), array('lang_id' => $lang_id,'label' => 'Général'))
            ->add('separator', 'text', array('label' => 'Séparateur'))
            ->add('class_active', 'text')
            ->add('displayHome', 'choice', array('label' => 'Lien vers l\'accueil','choices' => array(1 => 'Oui', 0 => 'Non')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\BlocBundle\Entity\BlocBreadcrumb', 'lang_id' => ''
        ));
    }

    public function getName()
    {
        return 'bloc_breadcrumb';
    }
}
