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
            case 'vertical':
                return $this->displayVertical($active);
            case 'horizontal':
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
                if(!$entry->getIsRoot()) {
                    switch ($entry->getLevel()) {
                        case 1:
                            if(count($entry->getChildren())) {
                                $hasChild = true;
                                foreach($entry->getChildren() as $child)
                                    $child_routes[] = $child->getNameRoute();
                            }
                            switch ($old_level) {
                                case 1:
                                    if (!$first) {
                                        $html .= '</li>';
                                    }
                                        
                                    if($entry->getNameRoute() == $this->getUrlIntern() || ($hasChild && in_array($this->getUrlIntern(), $child_routes))) {
                                        $classe = 'active';
                                    } else {
                                        $classe = '';
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
                                    if($entry->getNameRoute() == $this->getUrlIntern() || ($hasChild && in_array($this->getUrlIntern(), $child_routes))) {
                                        $classe = 'active';
                                    } else {
                                        $classe = '';
                                    }
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
        if (!empty($menus)) {
            $html = '<ul class="unstyled nav-vertical">';
            foreach ($menus as $entry) {
                switch ($entry->getLevel()) {
                    case 1:
                        if($entry->getTitle() == $active)
                            $classe = ' class="active"';
                        $html .= '<li><a href="'.$entry->getNameRoute().'"'.$classe.'>'.$entry->getTitle().'</a></li>';
                        break;    
                }
            }
        }
        return $html;
    }
}