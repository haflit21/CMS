<?php
namespace CMS\SitemapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\MenuBundle\Entity\MenuTaxonomy;

/**
 * CMS\SitemapBundle\Entity\Sitemap
 *
 * @ORM\Table(name="sitemap")
 * @ORM\Entity(repositoryClass="CMS\SitemapBundle\Entity\Repository\SitemapRepository")
 */
class Sitemap
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
     * @var boolean $display_title_menu
     *
     * @ORM\Column(name="display_title_menu", type="boolean")
     */
    private $display_title_menu;

    /**
     * @var string $class_columns
     *
     * @ORM\Column(name="class_columns", type="string", length=100)
     */
    private $class_columns;    

    /**
     * @ORM\ManyToMany(targetEntity="\CMS\MenuBundle\Entity\MenuTaxonomy", inversedBy="sitemaps")
     * @ORM\JoinTable(name="Sitemap_menuTaxonomy_relation")
     */
    private $menus_taxonomy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menus_taxonomy = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * @return Sitemap
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * Add menus_taxonomy
     *
     * @param \CMS\MenuBundle\MenuTaxonomy $menusTaxonomy
     * @return Sitemap
     */
    public function addMenusTaxonomy(\CMS\MenuBundle\Entity\MenuTaxonomy $menusTaxonomy)
    {
        $this->menus_taxonomy[] = $menusTaxonomy;
    
        return $this;
    }

    /**
     * Remove menus_taxonomy
     *
     * @param \CMS\MenuBundle\MenuTaxonomy $menusTaxonomy
     */
    public function removeMenusTaxonomy(\CMS\MenuBundle\Entity\MenuTaxonomy $menusTaxonomy)
    {
        $this->menus_taxonomy->removeElement($menusTaxonomy);
    }

    /**
     * Get menus_taxonomy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenusTaxonomy()
    {
        return $this->menus_taxonomy;
    }

    /**
     * Set display_title_menu
     *
     * @param boolean $displayTitleMenu
     * @return Sitemap
     */
    public function setDisplayTitleMenu($displayTitleMenu)
    {
        $this->display_title_menu = $displayTitleMenu;
    
        return $this;
    }

    /**
     * Get display_title_menu
     *
     * @return boolean 
     */
    public function getDisplayTitleMenu()
    {
        return $this->display_title_menu;
    }

    /**
     * Set class_columns
     *
     * @param string $classColumns
     * @return Sitemap
     */
    public function setClassColumns($classColumns)
    {
        $this->class_columns = $classColumns;
    
        return $this;
    }

    /**
     * Get class_columns
     *
     * @return string 
     */
    public function getClassColumns()
    {
        return $this->class_columns;
    }
}