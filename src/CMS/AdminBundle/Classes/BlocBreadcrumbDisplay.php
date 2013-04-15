<?php

namespace CMS\AdminBundle\Classes;

use CMS\AdminBundle\Classes\BlocDisplay;

class BlocBreadcrumbDisplay extends \CMS\AdminBundle\Classes\BlocDisplay
{

	public function __construct()
	{
		parent::__construct();
	}

	public function displayBloc()
    {
        $options = $this->getOptionsBreadcrumb();
        $entries = $options['entries'];
        $str = '<div class="breadcrumb_admin">';
        if ($options['url'] != $options['default_url']) {
            if($this->getBloc()->getDisplayHome())
                $str .= '<a href="/admin/dashboard"><i class="icon-home"></i></a>'.$this->getBloc()->getSeparator();
            $i=0;
            $nbLeaves = count($entries);
            foreach ($entries as $entry) { 
                if($i == $nbLeaves-1)
                    $str .= $entry->getTitle();
                else
                    $str .= '<a href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';

                if($i < $nbLeaves-1)
                    $str .= $this->getBloc()->getSeparator();
                $i++;
            }
        }
        $str .= '</div>';

        return $str;
    }
}