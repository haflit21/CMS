<?php
namespace CMS\SitemapBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Environment;

use CMS\MenuBundle\Menu;

class SitemapExtension extends Twig_Extension
{

	private $environment = null;
    private $menu_repo = null;
    private $sitemap_repo = null;
    

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function __construct($menu_repo, $sitemap_repo)
    {
        $this->menu_repo = $menu_repo;
        $this->sitemap_repo = $sitemap_repo;
    }

    public function getFilters()
    {
        return array('sitemap' => new Twig_Filter_Method($this, 'sitemapFilter'));
    }

    public function sitemapFilter($value) 
    {
        //TAG : [%%sitemap id:1%%]
        preg_match_all('/\[%%sitemap id:([0-9]+)%%]/', $value, $values, PREG_PATTERN_ORDER);

        if(!empty($values[0])) {    
            $tag = $values[0][0];
            $id = $values[1][0];
            $sitemap = $this->sitemap_repo->find($id);
            $menuTax = $sitemap->getMenusTaxonomy();
            $str = '';
            foreach ($menuTax as $key => $menu) {
                $entries = $menu->getMenus();
                $class = ($sitemap->getClassColumns() != '') ? ' class="'.$sitemap->getClassColumns().'"' : '';
                
                $str .= '<div'.$class.'>';
                if($sitemap->getDisplayTitleMenu())
                    $str .= '<h3>'.$menu->getName().'</h3>';
                
                $str .='<ul class="unstyled sitemap'.$class.'" id="menu-'.$menu->getId().'">';
                foreach ($entries as $entry) {
                    if(!$entry->getIsRoot())
                    $str .= '<li class="level-'.$entry->getLevel().'"><a href="/'.$entry->getLanguage()->getCode().'/'.$entry->getUrl().'">'.$entry->getTitle().'</a></li>';
                }
                $str .= '</ul>';
                $str .= '</div>';
            }
            $str .= '<div class="clearfix"></div>';
            return str_replace($tag, $str, $value);
        }
        return $value;
    }

    public function getName()
    {
        return 'sitemap_extension';
    }
}    