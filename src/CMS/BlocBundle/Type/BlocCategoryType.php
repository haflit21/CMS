<?php
namespace CMS\BlocBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CMS\BlocBundle\Type\BlocType;

use Doctrine\ORM\EntityRepository;

class BlocCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang_id = $options['lang_id'];
        $builder
            ->add('bloc', new BlocType(), array('lang_id' => $lang_id,'label' => 'Général'))
            ->add('category', 'entity', array(
                    'class' => 'CMSContentBundle:CMCategory',
                    'query_builder' =>
                        function(EntityRepository $er) use ($lang_id) {
                            return $er->getCategoryByLangIdQuery($lang_id);
                        },
                    'property' => 'title'
                    )
                )
            ->add('nbArticles', 'text', array('label' => 'Nombre d\'articles à afficher'))
            ->add('fieldOrder', 'entity', array(
                     'class' => 'CMSContentBundle:CMField',
                    'query_builder' =>
                        function(EntityRepository $er) use ($lang_id) {
                            return $er->createQueryBuilder('m')->orderBy('m.title', 'ASC');
                        },
                    'property' => 'title'
                    )
                )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\BlocBundle\Entity\BlocCategory', 'lang_id' => ''
        ));
    }

    public function getName()
    {
        return 'bloc_category';
    }
}
