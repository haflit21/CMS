<?php

namespace CMS\BlocBundle\Entity;

use CMS\ContentBundle\Entity\CMCategory;
use CMS\ContentBundle\Entity\CMContent;
use CMS\ContentBundle\Entity\CMLanguage;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAF\BlocBundle\Entity\Bloc
 *
 * @ORM\Table("bloc")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocRepository")
 */
class Bloc
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
     * @var string $position
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string $params
     *
     * @ORM\Column(name="params", type="string", length=255, nullable=true)
     */
    private $params;

    /**
     * @var string $ordre
     *
     * @ORM\Column(name="ordre_bloc", type="string", length=255, nullable=true)
     */
    private $ordre;

    /**
     * @var boolean $published
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var boolean $all_published
     *
     * @ORM\Column(name="all_published", type="boolean", length=255, nullable=true)
     */
    private $all_published;

    /**
     * @ORM\ManyToMany(targetEntity="CMS\ContentBundle\Entity\CMCategory")
     * @ORM\JoinTable(name="bloc_category")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="CMS\ContentBundle\Entity\CMContent")
     * @ORM\JoinTable(name="bloc_content")
     */
    private $contents;

    /**
     * @ORM\ManyToOne(targetEntity="CMS\ContentBundle\Entity\CMLanguage", inversedBy="blocs")
     * @ORM\JoinColumn(name="language_id")
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="Bloc", mappedBy="referenceBloc")
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Bloc", inversedBy="translations")
     */
    private $referenceBloc;

    /**
     * @ORM\ManyToOne(targetEntity="BlocTaxonomy", inversedBy="blocs")
     */
    private $taxonomy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->all_published = 1;
        $this->display_title = 0;
        $this->published = 0;

        return $this;
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
     * @return Bloc
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
     * Set position
     *
     * @param  string $position
     * @return Bloc
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Bloc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set params
     *
     * @param  string $params
     * @return Bloc
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set ordre
     *
     * @param  string $ordre
     * @return Bloc
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set published
     *
     * @param  boolean $published
     * @return Bloc
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
     * Set all_published
     *
     * @param  boolean $allPublished
     * @return Bloc
     */
    public function setAllPublished($allPublished)
    {
        $this->all_published = $allPublished;

        return $this;
    }

    /**
     * Get all_published
     *
     * @return boolean
     */
    public function getAllPublished()
    {
        return $this->all_published;
    }

    /**
     * Add categories
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $categories
     * @return Bloc
     */
    public function addCategorie(\CMS\ContentBundle\Entity\CMCategory $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \CMS\ContentBundle\Entity\CMCategory $categories
     */
    public function removeCategorie(\CMS\ContentBundle\Entity\CMCategory $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories(\Doctrine\Common\Collections\ArrayCollection $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Add contents
     *
     * @param  \CMS\ContentBundle\Entity\CMContent $contents
     * @return Bloc
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

    public function setContents(\Doctrine\Common\Collections\ArrayCollection $contents)
    {
        $this->contents = $contents;
    }

    /**
     * Set language
     *
     * @param  \CMS\ContentBundle\Entity\CMLanguage $language
     * @return Bloc
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
     * Add translations
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $translations
     * @return Bloc
     */
    public function addTranslation(\CMS\BlocBundle\Entity\Bloc $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \CMS\BlocBundle\Entity\Bloc $translations
     */
    public function removeTranslation(\CMS\BlocBundle\Entity\Bloc $translations)
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
     * Set referenceBloc
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $referenceBloc
     * @return Bloc
     */
    public function setReferenceBloc(\CMS\BlocBundle\Entity\Bloc $referenceBloc = null)
    {
        $this->referenceBloc = $referenceBloc;

        return $this;
    }

    /**
     * Get referenceBloc
     *
     * @return \CMS\BlocBundle\Entity\Bloc
     */
    public function getReferenceBloc()
    {
        return $this->referenceBloc;
    }

    /**
     * Add bloc_menu
     *
     * @param  $bloc
     */
    public function addBloc($bloc)
    {
        $this->blocs[] = $bloc;
    }

    /**
     * Get bloc_menu
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getBlocs()
    {
        return $this->blocs;
    }

}