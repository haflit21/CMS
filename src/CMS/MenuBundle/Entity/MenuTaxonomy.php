<?php

namespace CMS\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use CMS\SitemapBundle\Entity\Sitemap;

/**
 * CAF\MenuBundle\Entity\MenuTaxonomy
 *
 * @ORM\Table(name="menutaxonomy")
 * @ORM\Entity
 */
class MenuTaxonomy
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $alias
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="id_menu_taxonomy", cascade="remove")
     * @ORM\OrderBy({"root"="ASC", "lft"="ASC"})
     */
    protected $menus;

    /**
     * @ORM\Column(name="is_menu_admin", type="boolean")
     */
    private $is_menu_admin;

    /**
     * @ORM\ManyToMany(targetEntity="\CMS\SitemapBundle\Entity\Sitemap", mappedBy="menus_taxonomy")
     */
    private $sitemaps;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        if ($alias != '') {
            $this->alias = $alias;
        } else {
            $this->alias = $this->stringURLSafe($this->getName());
        }

    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    public function __construct()
    {
        $this->menus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add menus
     *
     * @param CAF\AdminBundle\Entity\Menu $menus
     */
    public function addMenu(\CMS\MenuBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;
    }

    /**
     * Get menus
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    private function transliterate($string)
    {
        $string = htmlentities(utf8_decode($string));
        $string = preg_replace(
            array('/&szlig;/','/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
            array('ss',"$1","$1".'e',"$1"),
            $string);

        return $string;
    }

    private function stringURLSafe($string)
    {
        //remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $string);

        $str = $this->transliterate($str);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));

        return $str;
    }

    /**
     * Remove menus
     *
     * @param CMS\MenuBundle\Entity\Menu $menus
     */
    public function removeMenu(\CMS\MenuBundle\Entity\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Set is_menu_admin
     *
     * @param boolean $isMenuAdmin
     * @return MenuTaxonomy
     */
    public function setIsMenuAdmin($isMenuAdmin)
    {
        $this->is_menu_admin = $isMenuAdmin;
    
        return $this;
    }

    /**
     * Get is_menu_admin
     *
     * @return boolean 
     */
    public function getIsMenuAdmin()
    {
        return $this->is_menu_admin;
    }
}