<?php
namespace CMS\FrontBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;

use CMS\ContactBundle\Entity\Repository\ContactRepository;
use CMS\ContactBundle\Type\FormFactory;

class FormContactExtension extends Twig_Extension
{

    private $contact_repo;
    private $form_factory;
    private $environment = null;

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function __construct(ContactRepository $contact_repo, FormFactory $form_factory)
    {
        $this->contact_repo = $contact_repo;
        $this->form_factory = $form_factory;
    }

    public function getFilters()
    {
        return array('formContact' => new Twig_Filter_Method($this, 'formContactFilter'));
    }

    public function formContactFilter($value,$lang)
    {

        preg_match_all('/\[%%[^%%]*%%\]/', $value, $vars_found, PREG_PATTERN_ORDER);
        $vars_found = current($vars_found);

        if (is_array($vars_found)) {
            foreach ($vars_found as $var) {
                $form = $this->form_factory->createForm();
                $view = $form->createView();

                return $this->environment->render('CMSContactBundle:front:contact.html.twig',array('form' => $view));
            }
        }

        return $value;
    }

    public function getName()
    {
        return 'formContact_extension';
    }
}
