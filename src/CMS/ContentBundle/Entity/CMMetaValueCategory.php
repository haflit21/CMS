<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FieldValue
 *
 * @ORM\Table(name="cm_metavalues_category")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class CMMetaValueCategory
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMCategory", inversedBy="metavalues", cascade={"persist"})
     */
    private $category;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMMeta", inversedBy="metavaluescategory")
     */
    private $meta;

    /**
     * @var string value
     *
     * @ORM\Column(name="value",type="string", nullable=true)
     */
    private $value;

    /**
     * Set value
     *
     * @param string $value
     * @return CMMetaValueCategory
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set category
     *
     * @param \CMS\ContentBundle\Entity\CMCategory $category
     * @return CMMetaValueCategory
     */
    public function setCategory(\CMS\ContentBundle\Entity\CMCategory $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \CMS\ContentBundle\Entity\CMCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set meta
     *
     * @param \CMS\ContentBundle\Entity\CMMeta $meta
     * @return CMMetaValueCategory
     */
    public function setMeta(\CMS\ContentBundle\Entity\CMMeta $meta)
    {
        $this->meta = $meta;
    
        return $this;
    }

    /**
     * Get meta
     *
     * @return \CMS\ContentBundle\Entity\CMMeta
     */
    public function getMeta()
    {
        return $this->meta;
    }


    public function __toString()
    {
        return $this->category->getName();
    }

}