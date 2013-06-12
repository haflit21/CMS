<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FieldValue
 *
 * @ORM\Table(name="cm_metavalues_content")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
*/
class CMMetaValueContent
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMContent", inversedBy="metavalues")
     */
    private $content;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMMeta", inversedBy="metavaluescontent")
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
     * @return CMMetaValueContent
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
     * Set content
     *
     * @param \CMS\ContentBundle\Entity\CMContent $content
     * @return CMMetaValueContent
     */
    public function setContent(\CMS\ContentBundle\Entity\CMContent $content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return \CMS\ContentBundle\Entity\CMContent 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set meta
     *
     * @param \CMS\ContentBundle\Entity\CMMeta $meta
     * @return CMMetaValueContent
     */
    public function setMeta(\CMS\ContentBundle\Entity\CMMeta $meta)
    {
        $this->meta = $meta;
    
        return $this;
    }

    /**
     * Get meta
     *
     * @return \CMS\ContentBundle\Entity\CMMetaContent 
     */
    public function getMeta()
    {
        return $this->meta;
    }
}
