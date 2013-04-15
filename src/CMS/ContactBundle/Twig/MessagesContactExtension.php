<?php
namespace CMS\ContactBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;

use CMS\ContactBundle\Entity\Repository\ContactRepository;

class MessagesContactExtension extends Twig_Extension
{
    private $contact_repo;

    public function __construct(ContactRepository $contact_repo)
    {
        $this->contact_repo = $contact_repo;
    }

    public function getFilters()
    {
        return array('messagesContact' => new Twig_Filter_Method($this, 'messagesContactFilter'));
    }

    public function messagesContactFilter($value)
    {
        $str = '<span class="alert-msg">';
        $nb = $this->contact_repo->getNbMessageNotRead();
        if($nb == 0)

            return '';
        $str .= $nb;
        $str .= '</span>';

        return $str;
    }

    public function getName()
    {
        return 'messagesContact_extension';
    }
}
