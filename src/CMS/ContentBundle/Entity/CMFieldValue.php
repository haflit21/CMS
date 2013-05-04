<?php

namespace CMS\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FieldValue
 *
 * @ORM\Table(name="cm_fieldsvalues")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
*/
class CMFieldValue
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMContent", inversedBy="fieldsvalues", cascade={"remove", "persist"})
     */
    private $content;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CMField", inversedBy="fieldsvalues")
     */
    private $field;

    /**
     * @var string value
     *
     * @ORM\Column(name="value",type="string")
     */
    private $value;

    /**
     * @ORM\PostLoad
     */
    public function unserializeFieldValue()
    {
        $this->value = unserialize($this->value);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serializeFieldValue()
    {
        $this->value = serialize($this->value);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fieldsvalue = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set value
     *
     * @param  string       $value
     * @return CMFieldValue
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
     * @param  \CMS\ContentBundle\Entity\CMContent $content
     * @return CMFieldValue
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
     * Set field
     *
     * @param  \CMS\ContentBundle\Entity\CMField $field
     * @return CMFieldValue
     */
    public function setField(\CMS\ContentBundle\Entity\CMField $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \CMS\ContentBundle\Entity\CMField
     */
    public function getField()
    {
        return $this->field;
    }
}