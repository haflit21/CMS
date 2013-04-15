<?php
namespace CMS\MenuBundle\Type\Type;

use Symfony\Component\Form\AbstractType;

class PublishType extends AbstractType
{

    private $publishChoices;

    public function __construct(array $publishChoices)
    {
        $this->publishChoices = $publishChoices;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'choices' => $this->publishChoices,
            'expanded' => true
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'publish';
    }
}
