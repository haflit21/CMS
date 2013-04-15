<?php
namespace CMS\ContactBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname')
                ->add('firstname')
                ->add('sender')
                ->add('subject')
                ->add('message', 'textarea');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContactBundle\Entity\Contact'
        ));
    }

    public function getName()
    {
        return 'contact';
    }

    public function getExtendedType()
    {
        return 'contact_form';
    }

}
