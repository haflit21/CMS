<?php
namespace CMS\DashboardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('date_debut','hidden')
                ->add('date_fin','hidden')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'CMS\DashboardBundle\Entity\Event');
    }

    public function getName()
    {
        return 'event';
    }
}
