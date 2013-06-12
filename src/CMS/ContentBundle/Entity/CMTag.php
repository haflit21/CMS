<?php

namespace CMS\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * CMS\ContentBundle\Entity\Content
 *
 * @ORM\Table(name="cm_tags")
 * @ORM\Entity(repositoryClass="CMS\ContentBundle\Entity\Repository\TagRepository")
 */
class CMTag
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
     * @ORM\ManyToMany(targetEntity="CMContent", mappedBy="tags")
     */
    private $contents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $title
     * @return CMTag
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
     * Add contents
     *
     * @param \CMS\ContentBundle\Entity\CMContent $contents
     * @return CMTag
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
     * Retourne le lien vers le tag
     */
    public function __toString()
    {
        return '<a href="/tag/'.$this->getSlug().'">'.$this->getTitle().'</a>';
    }
    

    /**
     * Set slug
     *
     * @param string $slug
     * @return CMTag
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
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
}
