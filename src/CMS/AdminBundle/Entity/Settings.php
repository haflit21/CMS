<?php

namespace CMS\AdminBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CMS\AdminBundle\Entity\Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="CMS\AdminBundle\Entity\Repository\SettingsRepository")
 */
class Settings
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
	 * @var string $option_name
	 *
	 * @ORM\Column(name="option_name", type="string", length=100, nullable=false)
	 */
	private $option_name;

    /**
     * @var string $type_field
     *
     * @ORM\Column(name="type_field", type="string", length=100, nullable=false)
     */
    private $type_field;

	/**
	 * @var string $option_value
	 *
	 * @ORM\Column(name="option_value", type="string", length=255, nullable=true)
	 */
	private $option_value;


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
     * Set option_name
     *
     * @param string $optionName
     * @return Settings
     */
    public function setOptionName($optionName)
    {
        $this->option_name = $optionName;
    
        return $this;
    }

    /**
     * Get option_name
     *
     * @return string 
     */
    public function getOptionName()
    {
        return $this->option_name;
    }

    /**
     * Set option_value
     *
     * @param string $optionValue
     * @return Settings
     */
    public function setOptionValue($optionValue)
    {
        $this->option_value = $optionValue;
    
        return $this;
    }

    /**
     * Get option_value
     *
     * @return string 
     */
    public function getOptionValue()
    {
        return $this->option_value;
    }

    /**
     * Set type_field
     *
     * @param string $typeField
     * @return Settings
     */
    public function setTypeField($typeField)
    {
        $this->type_field = $typeField;
    
        return $this;
    }

    /**
     * Get type_field
     *
     * @return string 
     */
    public function getTypeField()
    {
        return $this->type_field;
    }
}