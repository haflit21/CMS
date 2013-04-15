<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentTaxonomy
 *
 * @ORM\Table(name="bloc_taxonomy")
 * @ORM\Entity()
 */
class BlocTaxonomy
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Bloc", mappedBy="taxonomy",cascade={"persist"})
     */
    private $blocs;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add blocs
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $blocs
     * @return BlocTaxonomy
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

    /**
     * Set name
     *
     * @param  string       $name
     * @return BlocTaxonomy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}