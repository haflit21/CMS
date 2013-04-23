<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\MenuBundle\Entity\Menu;

/**
 * CMS\ContentBundle\Entity\Language
 *
 * @ORM\Table(name="cm_languages")
 * @ORM\Entity(repositoryClass="CMS\ContentBundle\Entity\Repository\LanguageRepository")
 */
class CMLanguage
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
     * @var string $iso
     *
     * @ORM\Column(name="iso", type="string", length=255)
     */
    private $iso;

    /**
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var boolean $default_lan
     *
     * @ORM\Column(name="default_lan", type="boolean")
     */
    private $default_lan;

    /**
     * @ORM\OneToMany(targetEntity="CMCategory", mappedBy="language")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="CMContent", mappedBy="language")
     */
    private $contents;

    /**
     * @ORM\OneToMany(targetEntity="\CMS\MenuBundle\Entity\Menu", mappedBy="language")
     */
    private $menus;

    /**
     * @ORM\OneToMany(targetEntity="\CMS\BlocBundle\Entity\Bloc", mappedBy="language")
     */
    private $blocs;

    public function __construct()
    {
        $this->published = 0;
        $this->default_lan = 0;
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
     * @return CMLanguage
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
     * Set iso
     *
     * @param  string     $iso
     * @return CMLanguage
     */
    public function setIso($iso)
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * Get iso
     *
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * Set published
     *
     * @param  boolean    $published
     * @return CMLanguage
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
     * Set default_lan
     *
     * @param  boolean    $defaultLan
     * @return CMLanguage
     */
    public function setDefaultLan($defaultLan)
    {
        $this->default_lan = $defaultLan;

        return $this;
    }

    /**
     * Get default_lan
     *
     * @return boolean
     */
    public function getDefaultLan()
    {
        return $this->default_lan;
    }

    /**
     * Add categories
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $categories
     * @return CMLanguage
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

    /**
     * Add contents
     *
     * @param  \CMS\ContentBundle\Entity\CMContent $contents
     * @return CMLanguage
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
     * Add menus
     *
     * @param  \CMS\MenuBundle\Entity\Menu $menus
     * @return CMLanguage
     */
    public function addMenu(\CMS\MenuBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \CMS\MenuBundle\Entity\Menu $menus
     */
    public function removeMenu(\CMS\MenuBundle\Entity\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Add blocs
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $blocs
     * @return CMLanguage
     */
    public function addBloc(\CMS\BlocBundle\Entity\Bloc $blocs)
    {
        $this->blocs[] = $blocs;

        return $this;
    }

    /**
     * Remove blocs
     *
     * @param \CMS\BlocBundle\Entity\Bloc $blocs
     */
    public function removeBloc(\CMS\BlocBundle\Entity\Bloc $blocs)
    {
        $this->blocs->removeElement($blocs);
    }

    /**
     * Get blocs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocs()
    {
        return $this->blocs;
    }

    public function __toString()
    {
        return $this->title;
    }
}