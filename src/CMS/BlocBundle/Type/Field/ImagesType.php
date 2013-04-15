<?php
namespace CMS\BlocBundle\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImagesType extends AbstractType
{

    private $imageOptions;

    public function __construct(array $imageOptions)
    {
        $this->imageOptions = $imageOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', 'hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'lang_id' => '', 'data' => array()
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'images';
    }
}
