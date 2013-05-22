<?php
namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CMS\ContentBundle\Type\DataTransformer\TagToStringTransformer;

class TagType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TagToStringTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'attr' => array('class' => 'input-tags', 'data-provide' => 'tag')
        ));
    }


    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'tag_selector';
    }
}