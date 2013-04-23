<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CMS\ContentBundle\Entity\CMCategory
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="cm_categories")
 * @ORM\Entity(repositoryClass="CMS\ContentBundle\Entity\Repository\CategoryRepository")
 */
class CMCategory
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="CMCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var entity ordre
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="CMCategory", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="CMLanguage", inversedBy="categories")
     * @ORM\JoinColumn(name="language_id")
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity="CMContent", mappedBy="categories")
     */
    private $contents;

    /**
     * @var string $metatitle
     *
     * @ORM\Column(name="metatitle", type="string", length=255)
     */
    private $metatitle;

    /**
     * @var text $metadescription
     *
     * @ORM\Column(name="metadescription", type="text", nullable=true)
     */
    private $metadescription;

    /**
     * @var string $canonical
     *
     * @ORM\Column(name="canonical", type="string", length=255, nullable=true)
     */
    private $canonical;

    /**
     * @var string url
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
     private $url;

    /**
     * @ORM\OneToMany(targetEntity="CMCategory", mappedBy="referenceCategory")
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="CMCategory", inversedBy="translations")
     */
    private $referenceCategory;

    /**
     * @var boolean published
     *
     * @ORM\Column(name="published", type="boolean")
     */
     private $published;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tutorials = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param  string     $title
     * @return CMCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param  string     $description
     * @return CMCategory
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add contents
     *
     * @param  \CMS\ContentBundle\Entity\CMContent $contents
     * @return CMCategory
     */
    public function addContent(\CMS\ContentBundle\Entity\CMContent $contents)
    {
        $this->contents[] = $contents;

        return $this;
    }

    /**
     * Remove contents
     *
     * @param \CMS\ContentBundle\Entity\CMContent $contents
     */
    public function removeContent(\CMS\ContentBundle\Entity\CMContent $contents)
    {
        $this->contents->removeElement($contents);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set metatitle
     *
     * @param  string     $metatitle
     * @return CMCategory
     */
    public function setMetatitle($metatitle)
    {
        $this->metatitle = $metatitle;

        return $this;
    }

    /**
     * Get metatitle
     *
     * @return string
     */
    public function getMetatitle()
    {
        return $this->metatitle;
    }

    /**
     * Set metadescription
     *
     * @param  string     $metadescription
     * @return CMCategory
     */
    public function setMetadescription($metadescription)
    {
        $this->metadescription = $metadescription;

        return $this;
    }

    /**
     * Get metadescription
     *
     * @return string
     */
    public function getMetadescription()
    {
        return $this->metadescription;
    }

    /**
     * Set canonical
     *
     * @param  string     $canonical
     * @return CMCategory
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;

        return $this;
    }

    /**
     * Get canonical
     *
     * @return string
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * Set url
     *
     * @param  string     $url
     * @return CMCategory
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Add translations
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $translations
     * @return CMCategory
     */
    public function addTranslation(\CMS\ContentBundle\Entity\CMCategory $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \CMS\ContentBundle\Entity\CMCategory $translations
     */
    public function removeTranslation(\CMS\ContentBundle\Entity\CMCategory $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set referenceCategory
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $referenceCategory
     * @return CMCategory
     */
    public function setReferenceCategory(\CMS\ContentBundle\Entity\CMCategory $referenceCategory = null)
    {
        $this->referenceCategory = $referenceCategory;

        return $this;
    }

    /**
     * Get referenceCategory
     *
     * @return \CMS\ContentBundle\Entity\CMCategory
     */
    public function getReferenceCategory()
    {
        return $this->referenceCategory;
    }

    /**
     * Set language
     *
     * @param  \CMS\ContentBundle\Entity\CMLanguage $language
     * @return CMCategory
     */
    public function setLanguage(\CMS\ContentBundle\Entity\CMLanguage $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \CMS\ContentBundle\Entity\CMLanguage
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set lft
     *
     * @param  integer    $lft
     * @return CMCategory
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param  integer    $rgt
     * @return CMCategory
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set ordre
     *
     * @param  integer    $ordre
     * @return CMCategory
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set root
     *
     * @param  integer    $root
     * @return CMCategory
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set level
     *
     * @param  integer    $level
     * @return CMCategory
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set parent
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $parent
     * @return CMCategory
     */
    public function setParent(\CMS\ContentBundle\Entity\CMCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CMS\ContentBundle\Entity\Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param  \CMS\ContentBundle\Entity\Menu $children
     * @return CMCategory
     */
    public function addChildren(\CMS\ContentBundle\Entity\CMCategory $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \CMS\ContentBundle\Entity\Menu $children
     */
    public function removeChildren(\CMS\ContentBundle\Entity\CMCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function __toString()
    {
        return str_repeat('-', $this->getLevel()).' '.$this->getTitle();
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return CMCategory
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }
}