<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\MenuBundle\Entity\MenuTaxonomy;

/**
 * CAF\BlocBundle\Entity\BlocBreadcrumb
 *
 * @ORM\Table(name="BlocBreadcrumb")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocBreadcrumbRepository")
 */
class BlocBreadcrumb
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

    /**
     * @ORM\Column(name="separateur", type="string", length=50)
     */
    private $separator;

    /**
     * @ORM\Column(name="class_active", type="string", length=50)
     */
    private $class_active;

    /**
     * @ORM\Column(name="display_home", type="boolean")
     */
    private $displayHome;

    /**
     * @ORM\OneToOne(targetEntity="\CMS\MenuBundle\Entity\MenuTaxonomy")
     */
    private $menuTaxonomy;

    private $url;

    public function displayBloc($options = null)
    {
        $entries = $options['entries'];
        $str = '<div class="container"><ul class="breadcrumb">';
        if ($options['url'] != $options['default_url']) {
            if($this->displayHome)
                $str .= '<li><a href="'.$options['default_url'].'"><i class="icon-home"></i></a>'.$this->separator.'</li>';
            $i=0;
            $nbLeaves = count($entries);
            foreach ($entries as $entry) {
                if($i == $nbLeaves-1)
                    $str .= '<li class="'.$this->class_active.'">'.$entry->getTitle();
                else
                    $str .= '<li><a href="'.$entry->getUrl().'">'.$entry->getTitle().'</a>';

                if($i < $nbLeaves-1)
                    $str .= $this->separator;
                $str .= '</li>';
                $i++;
            }
        }
        $str .= '</ul></div>';

        return $str;
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
     * Set separator
     *
     * @param  string         $separator
     * @return BlocBreadcrumb
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Get separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Set class_active
     *
     * @param  string         $classActive
     * @return BlocBreadcrumb
     */
    public function setClassActive($classActive)
    {
        $this->class_active = $classActive;

        return $this;
    }

    /**
     * Get class_active
     *
     * @return string
     */
    public function getClassActive()
    {
        return $this->class_active;
    }

    /**
     * Set displayHome
     *
     * @param  boolean        $displayHome
     * @return BlocBreadcrumb
     */
    public function setDisplayHome($displayHome)
    {
        $this->displayHome = $displayHome;

        return $this;
    }

    /**
     * Get displayHome
     *
     * @return boolean
     */
    public function getDisplayHome()
    {
        return $this->displayHome;
    }

    /**
     * Set bloc
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $bloc
     * @return BlocBreadcrumb
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
        return 'BlocBreadcrumb';
    }

    /**
     * Set menuTaxonomy
     *
     * @param \CMS\BlocBundle\Entity\MenuTaxonomy $menuTaxonomy
     * @return BlocBreadcrumb
     */
    public function setMenuTaxonomy(\CMS\MenuBundle\Entity\MenuTaxonomy $menuTaxonomy = null)
    {
        $this->menuTaxonomy = $menuTaxonomy;
    
        return $this;
    }

    /**
     * Get menuTaxonomy
     *
     * @return \CMS\BlocBundle\Entity\MenuTaxonomy 
     */
    public function getMenuTaxonomy()
    {
        return $this->menuTaxonomy;
    }
}