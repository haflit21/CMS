<?php
namespace CMS\SitemapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\MenuBundle\MenuTaxonomy;

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
    public function addMenusTaxonomy(\CMS\MenuBundle\MenuTaxonomy $menusTaxonomy)
    {
        $this->menus_taxonomy[] = $menusTaxonomy;
    
        return $this;
    }

    /**
     * Remove menus_taxonomy
     *
     * @param \CMS\MenuBundle\MenuTaxonomy $menusTaxonomy
     */
    public function removeMenusTaxonomy(\CMS\MenuBundle\MenuTaxonomy $menusTaxonomy)
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
}