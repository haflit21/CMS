<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentType
 *
 * @ORM\Table(name="cm_contenttypes")
 * @ORM\Entity
 */
class CMContentType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

     /**
     * @ORM\ManyToMany(targetEntity="CMField", mappedBy="contentType")
     */
    private $fields;

    /**
     * @ORM\OneToMany(targetEntity="CMContent", mappedBy="fieldvalues")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255)
     */
    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * to_String
     */
    public function __toString()
    {
        return $this->title;
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
     * @param  string        $title
     * @return CMContentType
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
     * Set template
     *
     * @param  string        $template
     * @return CMContentType
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add fields
     *
     * @param  \CMS\ContentBundle\Entity\CMField $fields
     * @return CMContentType
     */
    public function addField(\CMS\ContentBundle\Entity\CMField $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param \CMS\ContentBundle\Entity\CMField $fields
     */
    public function removeField(\CMS\ContentBundle\Entity\CMField $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add content
     *
     * @param  \CMS\ContentBundle\Entity\CMContent $content
     * @return CMContentType
     */
    public function addContent(\CMS\ContentBundle\Entity\CMContent $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \CMS\ContentBundle\Entity\CMContent $content
     */
    public function removeContent(\CMS\ContentBundle\Entity\CMContent $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContent()
    {
        return $this->content;
    }
}