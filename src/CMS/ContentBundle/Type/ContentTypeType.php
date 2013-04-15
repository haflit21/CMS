<?php

namespace CMS\ContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentTypeType extends AbstractType
{

    private $templates;

    private function getTemplatePath()
    {
        $dirbase = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR));
        $dirbase .= DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'_templates';

        return $dirbase;
    }

    private function listTemplates()
    {
        $directorypath = $this->getTemplatePath();

        //open my directory
        $myDirectory = opendir($directorypath);

        // get each entry
        while ($template = readdir($myDirectory)) {
            if ($template != '.' && $template != '..') {
                $templates["$template"] = $template;
            }
        }
        //var_dump($templates[]);die();

        // close directory
        closedir($myDirectory);
        //sort($templates);
        return $templates;
    }

    public function __construct()
    {
        $this->templates = $this->listTemplates();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label'=>'Title'))
            ->add('template', 'choice', array('label'=>'Template', 'empty_value' => 'Choose your template', 'choices'=>$this->templates))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\ContentBundle\Entity\CMContentType'
        ));
    }

    public function getName()
    {
        return 'contentmanager_contenttype';
    }
}
