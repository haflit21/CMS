<?php

namespace CMS\AdminBundle\Classes;

use CMS\AdminBundle\Classes\BlocDisplay;

class BlocUserDisplay extends \CMS\AdminBundle\Classes\BlocDisplay
{

	public function __construct()
	{
		parent::__construct();
	}

	public function displayBloc($options=array())
    {
        $html = '<ul class="nav nav-horizontal bloc-user">';
        $html .= '<li class="dropdown">';
        $html .= '<a href="javascript:" class="dropdown-toggle" data-toggle="dropdown">'.$options['user'].' <i class="admin-icon-down-open-mini"></i></a>';
        $html .= '<ul class="dropdown-menu" role="menu">';
        $html .= '<li><a href="'.$options['site_url'].'" target="_blank">Voir le site <i class="admin-icon-logout pull-right"></i></a></li>';
        $html .= '<li class="divider"></li>';
        $html .= '<li><a href="/admin/logout">Se déconnecter <i class="admin-icon-off pull-right"></i></a></li>';
        $html .= '</ul>';
        $html .= '</li>';
        $html .=  '</ul>';
        return $html;
    }
}