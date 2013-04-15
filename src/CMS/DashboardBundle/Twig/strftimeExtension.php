<?php
namespace CMS\DashboardBundle\Twig;

class strftimeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'strftime' => new \Twig_Filter_Method($this, 'strftimeFilter'),
        );
    }

    public function strftimeFilter($date_param, $format, $locale='fr_FR')
    {
        setlocale(LC_ALL, $locale);
        if($date_param != 'now')
            $date = \DateTime::createFromFormat('m/d/Y',$date_param);
        else
            $date = new \DateTime();
        $timestamp = $date->getTimestamp();

        return strftime($format,$timestamp);

    }

    public function getName()
    {
        return 'strftime_extension';
    }
}
