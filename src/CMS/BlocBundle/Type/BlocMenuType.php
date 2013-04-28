<?php
namespace CMS\BlocBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CMS\BlocBundle\Type\BlocType;

use Doctrine\ORM\EntityRepository;

class BlocMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('bloc', new BlocType(), array('lang_id' => $lang_id,'label' => 'Général'))
            ->add('menu', 'entity', array(
                'class' => 'CMSMenuBundle:MenuTaxonomy',
                'query_builder' =>
                    function(EntityRepository $er) {
                        return $er->createQueryBuilder('m')->orderBy('m.name', 'ASC');
                    },
                'property' => 'name'
                    )
                )
            ->add('display_type',
                'choice', array( 'empty_value' => 'Choose a display format','choices' =>  array('header'=>'Menu Principal', 'footer'=>'Menu du bas', 'admin_h' => 'Administration Horizontal', 'admin_v' => 'Administration Vertical'))
                )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\BlocBundle\Entity\BlocMenu', 'lang_id' => ''
        ));
    }

    public function getName()
    {
        return 'bloc_menu';
    }
}
