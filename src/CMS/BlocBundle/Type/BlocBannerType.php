<?php
namespace CMS\BlocBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CMS\BlocBundle\Type\BlocType;

class BlocBannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('bloc', new BlocType(), array('lang_id' => $lang_id,'label' => ' '))
            ->add('slogans', 'textarea')
            ->add('images', 'images',
                    array(
                        'attr' => array('class' => 'values-images')
                        )
                );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\BlocBundle\Entity\BlocBanner', 'lang_id' => '', 'images' => array()
        ));
    }

    public function getName()
    {
        return 'bloc_banner';
    }
}
