<?php
namespace CMS\ContactBundle\Type;

use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements FactoryInterface
{
    private $formFactory;
    private $name;
    private $type;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->name = 'contact';
        $this->type = 'contact';
    }

    public function createForm()
    {
        return $this->formFactory->createNamed($this->name,new \CMS\ContactBundle\Type\ContactType(), null, array());
    }
}
