<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAF\BlocBundle\Entity\BlocUser
 *
 * @ORM\Table(name="blocuser")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocMenuRepository")
 */
class BlocUser
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
    * @ORM\ManyToOne(targetEntity="Bloc")
    */
    private $bloc;


    public function __construct()
    {
        $bloc = new Bloc();
        $this->setBloc($bloc);
    }

    public function displayBloc()
    {
        return '';
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
     * Set bloc
     *
     * @param \CMS\BlocBundle\Entity\Bloc $bloc
     * @return BlocUser
     */
    public function setBloc(\CMS\BlocBundle\Entity\Bloc $bloc = null)
    {
        $this->bloc = $bloc;
    
        return $this;
    }

    /**
     * Get bloc
     *
     * @return \CMS\BlocBundle\Entity\Bloc 
     */
    public function getBloc()
    {
        return $this->bloc;
    }

    public function getType()
    {
        return 'BlocUser';
    }
}