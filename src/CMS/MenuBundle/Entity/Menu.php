<?php

namespace CMS\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use CMS\ContentBundle\Entity\CMLanguage;
use CMS\ContentBundle\Entity\CMCategory;
use CMS\ContentBundle\Entity\CMContent;

/**
 * CMS\ContentBundle\Entity\Content
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="menus")
 * @ORM\Entity(repositoryClass="CMS\MenuBundle\Entity\Repository\MenuRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Menu
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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=64, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="published", type="boolean")
     */
    private $published=0;

    /**
     * @ORM\ManyToOne(targetEntity="\CMS\ContentBundle\Entity\CMCategory")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="\CMS\ContentBundle\Entity\CMContent")
     */
    private $content;

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
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
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
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     */
    private $children;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="referenceMenu")
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="translations")
     */
    private $referenceMenu;

    /**
     * @ORM\ManyToOne(targetEntity="\CMS\MenuBundle\Entity\MenuTaxonomy", inversedBy="menus")
     * @ORM\JoinColumn(name="id_menu_taxonomy", referencedColumnName="id")
     */
    private $id_menu_taxonomy;

    /**
     * @ORM\Column(name="default_page", type="boolean")
     **/
    private $default_page;

    /**
     * @ORM\ManyToOne(targetEntity="CMS\ContentBundle\Entity\CMLanguage", inversedBy="menus")
     * @ORM\JoinColumn(name="language_id")
     */
    private $language;

    /**
     * @ORM\Column(name="intern", type="boolean")
     */
    private $intern;


    /**
     * @ORM\Column(name="name_route", type="string", nullable=true)
     */
    private $name_route;

    /**
     * @ORM\Column(name="isRoot", type="boolean")
     */
    private $isRoot;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $classIcon;

    /**
     * @ORM\Column(type="boolean")
     */
    private $displayIcon;

    /**
     * @ORM\Column(type="boolean")
     */
    private $displayName;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents/'.date('m/Y');
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour générer un nom unique
            $this->path = $this->file->getClientOriginalName().'.'.$this->file->guessExtension();
        }
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        // utilisez le nom de fichier original ici mais
        // vous devriez « l'assainir » pour au moins éviter
        // quelconques problèmes de sécurité

        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

        // définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->path = $this->file->getClientOriginalName();

        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
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
     * @param  string $title
     * @return Menu
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
     * Set slug
     *
     * @param  string $slug
     * @return Menu
     */
    public function setSlug($slug)
    {
        if ($slug != '') {
            $this->slug = $slug;
        } else {
            $this->slug = $this->stringURLSafe($this->getTitle());
        }

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set published
     *
     * @param  boolean $published
     * @return Menu
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

    /**
     * Set category
     *
     * @param  integer $category
     * @return Menu
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set content
     *
     * @param  integer $content
     * @return Menu
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return integer
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set lft
     *
     * @param  integer $lft
     * @return Menu
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
     * @param  integer $rgt
     * @return Menu
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
     * @param  integer $ordre
     * @return Menu
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
     * @param  integer $root
     * @return Menu
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
     * @param  integer $level
     * @return Menu
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
     * Set created
     *
     * @param  \DateTime $created
     * @return Menu
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Menu
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set parent
     *
     * @param  \CMS\MenuBundle\Entity\Menu $parent
     * @return Menu
     */
    public function setParent(\CMS\MenuBundle\Entity\Menu $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CMS\MenuBundle\Entity\Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param  \CMS\MenuBundle\Entity\Menu $children
     * @return Menu
     */
    public function addChildren(\CMS\MenuBundle\Entity\Menu $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \CMS\MenuBundle\Entity\Menu $children
     */
    public function removeChildren(\CMS\MenuBundle\Entity\Menu $children)
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

    /**
     * Add translations
     *
     * @param  \CMS\MenuBundle\Entity\Menu $translations
     * @return Menu
     */
    public function addTranslation(\CMS\MenuBundle\Entity\Menu $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \CMS\MenuBundle\Entity\Menu $translations
     */
    public function removeTranslation(\CMS\MenuBundle\Entity\Menu $translations)
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
     * Set referenceContent
     *
     * @param  \CMS\MenuBundle\Entity\Menu $referenceContent
     * @return Menu
     */
    public function setReferenceContent(\CMS\MenuBundle\Entity\Menu $referenceContent = null)
    {
        $this->referenceContent = $referenceContent;

        return $this;
    }

    /**
     * Get referenceContent
     *
     * @return \CMS\MenuBundle\Entity\Menu
     */
    public function getReferenceContent()
    {
        return $this->referenceContent;
    }

    /**
     * Set menutaxonomy
     *
     * @param  \CMS\MenuBundle\Entity\MenuTaxonomy $menutaxonomy
     * @return Menu
     */
    public function setMenutaxonomy(\CMS\MenuBundle\Entity\MenuTaxonomy $menutaxonomy = null)
    {
        $this->menutaxonomy = $menutaxonomy;

        return $this;
    }

    /**
     * Get menutaxonomy
     *
     * @return \CMS\MenuBundle\Entity\MenuTaxonomy
     */
    public function getMenutaxonomy()
    {
        return $this->id_menu_taxonomy;
    }

    /**
     * Set referenceMenu
     *
     * @param  \CMS\MenuBundle\Entity\Menu $referenceMenu
     * @return Menu
     */
    public function setReferenceMenu(\CMS\MenuBundle\Entity\Menu $referenceMenu = null)
    {
        $this->referenceMenu = $referenceMenu;

        return $this;
    }

    /**
     * Get referenceMenu
     *
     * @return \CMS\MenuBundle\Entity\Menu
     */
    public function getReferenceMenu()
    {
        return $this->referenceMenu;
    }

    /**
     * Set language
     *
     * @param  \CMS\ContentBundle\Entity\CMLanguage $language
     * @return Menu
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
     * Set id_menu_taxonomy
     *
     * @param  \CMS\MenuBundle\Entity\MenuTaxonomy $idMenuTaxonomy
     * @return Menu
     */
    public function setIdMenuTaxonomy(\CMS\MenuBundle\Entity\MenuTaxonomy $idMenuTaxonomy = null)
    {
        $this->id_menu_taxonomy = $idMenuTaxonomy;

        return $this;
    }

    /**
     * Get id_menu_taxonomy
     *
     * @return \CMS\MenuBundle\Entity\MenuTaxonomy
     */
    public function getIdMenuTaxonomy()
    {
        return $this->id_menu_taxonomy;
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
     * Set default_page
     *
     * @param  boolean $defaultPage
     * @return Menu
     */
    public function setDefaultPage($defaultPage)
    {
        $this->default_page = $defaultPage;

        return $this;
    }

    /**
     * Get default_page
     *
     * @return boolean
     */
    public function getDefaultPage()
    {
        return $this->default_page;
    }

    public function __toString()
    {
        return str_repeat('-', $this->getLevel()).' '.$this->getTitle();
    }

    public function getUrl()
    {
        if (is_object($this->content)) {
            return $this->content->getUrl();
        }
        if (is_object($this->category)) {
          return $this->category->getUrl();
        }

    }

    /**
     * Set intern
     *
     * @param boolean $intern
     * @return Menu
     */
    public function setIntern($intern)
    {
        $this->intern = $intern;
    
        return $this;
    }

    /**
     * Get intern
     *
     * @return boolean 
     */
    public function getIntern()
    {
        return $this->intern;
    }

    /**
     * Set name_route
     *
     * @param string $nameRoute
     * @return Menu
     */
    public function setNameRoute($nameRoute)
    {
        $this->name_route = $nameRoute;
    
        return $this;
    }

    /**
     * Get name_route
     *
     * @return string 
     */
    public function getNameRoute()
    {
        return $this->name_route;
    }

    /**
     * Set isRoot
     *
     * @param boolean $isRoot
     * @return Menu
     */
    public function setIsRoot($isRoot)
    {
        $this->isRoot = $isRoot;
    
        return $this;
    }

    /**
     * Get isRoot
     *
     * @return boolean 
     */
    public function getIsRoot()
    {
        return $this->isRoot;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Menu
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set classIcon
     *
     * @param string $classIcon
     * @return Menu
     */
    public function setClassIcon($classIcon)
    {
        $this->classIcon = $classIcon;
    
        return $this;
    }

    /**
     * Get classIcon
     *
     * @return string 
     */
    public function getClassIcon()
    {
        return $this->classIcon;
    }

    /**
     * Set displayIcon
     *
     * @param boolean $displayIcon
     * @return Menu
     */
    public function setDisplayIcon($displayIcon)
    {
        $this->displayIcon = $displayIcon;
    
        return $this;
    }

    /**
     * Get displayIcon
     *
     * @return boolean 
     */
    public function getDisplayIcon()
    {
        return $this->displayIcon;
    }

    /**
     * Set displayName
     *
     * @param boolean $displayName
     * @return Menu
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    
        return $this;
    }

    /**
     * Get displayName
     *
     * @return boolean 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
}