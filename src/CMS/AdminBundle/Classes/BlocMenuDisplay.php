<?php

namespace CMS\AdminBundle\Classes;

use CMS\AdminBundle\Classes\BlocDisplay;

class BlocMenuDisplay extends \CMS\AdminBundle\Classes\BlocDisplay
{

	public function __construct()
	{
		parent::__construct();
	}

	public function displayBloc($options=array())
    {
        $session = $this->getSession();
        $active = $session->get('active', 'Contenus');
        switch($options['dir']) {
            case 'admin_v':
                return $this->displayVertical($active);
            case 'admin_h':
                return $this->displayHorizontal($active);
        }
    }

    public function displayHorizontal($active)
    {

        $classe = '';
        $old_level = 1;
        $first = true;
        $id_old = 0;
        $menus = $this->getBloc()->getMenu()->getMenus();
        $html = '';
        $hasChild = false;
        $child_routes = array();
        if (!empty($menus)) {
            $html .= '<ul class="nav">';
            foreach ($menus as $entry) {
                $hasChild = false;
                $child_routes = array();
                $classe = '';
                if(!$entry->getIsRoot()) {

                    switch ($entry->getLevel()) {
                        case 1:

                            
                            switch ($old_level) {
                                case 1:
                                    if (!$first) {
                                        $html .= '</li>';
                                    }
                                     
                                    
                                    if($hasChild) {
                                        $html .= '<li class="dropdown '.$classe.'">';
                                        $html .= '<a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown" href="'.$entry->getNameRoute().'">'.$entry->getTitle().' <b class="caret"></b></a>';
                                    } else {
                                        $html .= '<li class="'.$classe.'">';
                                        $html .= '<a id="drop-'.$entry->getId().'" href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';    
                                    }
                                    $first = false;
                                    break;
                                case 2:
                                    $html .= '</li></ul></li>';
                                    if(count($entry->getChildren())) {
                                        $html .= '<li class="dropdown '.$classe.'"><a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown"  href="'.$entry->getNameRoute().'">'.$entry->getTitle().' <b class="caret"></b></a>';
                                    } else {
                                        $html .= '<li class="'.$classe.'"><a id="drop-'.$entry->getId().'" href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                    }    
                                    break;
                            }
                            $id_old = $entry->getId();
                            break;
                        case  2:
                            switch ($old_level) {
                                case 1:
                                    $html .= '<ul class="dropdown-menu" aria-labelledby="drop-'.$id_old.'" role="menu"><li><a href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                    break;
                                case 2:
                                    $html .= '</li><li><a href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                    break;
                            }
                            break;
                    }
                    $old_level = $entry->getLevel();
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }


    public function displayVertical($active)
    {
        $classe = '';
        $menus = $this->getBloc()->getMenu()->getMenus();
        $html = '';
        $old_level = 1;
        $first = true;
        $parent_class = false;
        $hasChild = false;
        if (!empty($menus)) {
            $html = '<ul class="nav nav-list">';
            $html .= '<li class="header"><a href="javascript:">'.$this->getBloc()->getMenu()->getName().'</a></li>';
            foreach ($menus as $entry) {
                $displayIcon = false;
                $child_routes = array();
                
                $classe = '';
                if(!$entry->getIsRoot()) {
                    switch ($entry->getLevel()) {
                        case 1:
                            if (strpos($entry->getNameRoute(), $this->getUrlIntern()) !== false) {
                                $classe = ' class="active"';
                                $parent_class = true;
                            } else {
                                $parent_class = false;
                            }    
                            if(count($entry->getChildren()) && $classe != ' class="active"') {
                                $hasChild = true;
                                foreach($entry->getChildren() as $child) {
                                    if (strpos($child->getNameRoute(), $this->getUrlIntern()) !== false) {
                                        $classe = ' class="active"';
                                        $parent_class = true;
                                        break;
                                    }
                                }
                            }
                            switch ($old_level) {
                                case 1:
                                    if (!$first) {
                                        $html .= '</li>';
                                    }   
                                    

                                    $html .= '<li'.$classe.'><a href="'.$entry->getNameRoute().'"'.$classe.'>';
                                    if ($entry->getDisplayIcon()) {
                                        $displayIcon = true;
                                        if(!is_null($entry->getPath())) 
                                            $html .= '<img src="'.$entry->getWebPath().'" />';
                                        elseif (!is_null($entry->getClassIcon()))
                                            $html .= '<span class="'.$entry->getClassIcon().'"></span>';
                                    } 
                                    if ($entry->getDisplayName()) {
                                        $html .= $entry->getTitle();    
                                    }
                                    $html .= '<i class="admin-icon-right-open"></i>';
                                    $html .= '</a>';
                                    $first = false;
                                    break;
                                case 2:
                                    $html .= '</li></ul></li>'; 

                                    $html .= '<li'.$classe.'><a href="'.$entry->getNameRoute().'"'.$classe.'>';
                                    if ($entry->getDisplayIcon()) {
                                        $displayIcon = true;
                                        if(!is_null($entry->getPath())) 
                                            $html .= '<img src="'.$entry->getWebPath().'" />';
                                        elseif (!is_null($entry->getClassIcon()))
                                            $html .= '<span class="'.$entry->getClassIcon().'"></span>';
                                    } 
                                    if ($entry->getDisplayName()) {
                                        $html .= $entry->getTitle();    
                                    }
                                    $html .= '<i class="admin-icon-right-open"></i>';
                                    $html .= '</a>';
                                    break;
                            }

                            break;
                        case 2:
                            $sub_class ='';
                            if (strpos($entry->getNameRoute(), $this->getUrlIntern()) !== false)
                                $sub_class = ' class="active"';
                            
                            switch ($old_level) {    
                                case 1:
                                    if(!$parent_class)
                                        $classe = ' hide';
                                    else
                                        $classe = '';

                                    $html .= '<ul class="unstyled'.$classe.'"><li><a href="'.$entry->getNameRoute().'"'.$sub_class.'>'.$entry->getTitle().'</a>';
                                    break;
                                case 2:
                                    $html .= '</li><li><a href="'.$entry->getNameRoute().'"'.$sub_class.'>'.$entry->getTitle().'</a>';
                                    break;
                            }
                            break;  
                                    
                    }
                    $old_level = $entry->getLevel();  
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }
}