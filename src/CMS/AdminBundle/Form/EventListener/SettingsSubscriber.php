<?php
namespace CMS\AdminBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class SettingsSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $fields;

    public function __construct(FormFactoryInterface $factory, array $fields)
    {
        $this->factory = $factory;
        $this->fields = $fields;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData($event)
    {

        $data = $event->getData();
        $form = $event->getForm();
        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        foreach($this->fields as $field) {
            switch($field->getTypeField()) {
                case 'textarea':
                    $form->add(
                        $this->factory->createNamed(
                            $field->getOptionName(),
                            $field->getTypeField(), 
                            $field->getOptionValue(), 
                            array(
                                'required' => false, 
                                'label' => $field->getLabelField(),
                                'auto_initialize' => false,
                                'attr' => array('rows' => 5)
                            )
                        )
                    );
                    break;
                default:
                    $form->add(
                        $this->factory->createNamed(
                            $field->getOptionName(),
                            $field->getTypeField(), 
                            $field->getOptionValue(), 
                            array(
                                'required' => false, 
                                'label' => $field->getLabelField(),
                                'auto_initialize' => false
                            )
                        )
                    );    
                    break;
            }

              
        }
    }
}