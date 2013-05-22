<?php

namespace CMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use CMS\AdminBundle\Form\EventListener\SettingsSubscriber;



class SettingsType extends AbstractType
{


	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$options_fields = $options['options_fields'];
		
		$subscriber = new SettingsSubscriber($builder->getFormFactory(), $options_fields);
        $builder->addEventSubscriber($subscriber);

	}

	public function getDefaultOptions(array $options)
	{
		return array('data_class' => null, 'options_fields' => '');
	}

	public function getName()
	{
		return 'settings_form';
	}
}