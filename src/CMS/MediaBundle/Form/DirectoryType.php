<?php
namespace CMS\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DirectoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('directory', 'text', array('label' => 'Créer un répertoire'))
                ->add('current', 'hidden')
        ;
    }

    public function getName()
    {
        return 'directory_form';
    }
}
