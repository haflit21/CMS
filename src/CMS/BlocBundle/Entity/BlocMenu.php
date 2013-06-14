<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAF\BlocBundle\Entity\BlocMenu
 *
 * @ORM\Table(name="blocmenu")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocMenuRepository")
 */
class BlocMenu
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
    * @ORM\OneToOne(targetEntity="CMS\MenuBundle\Entity\MenuTaxonomy")
    */
    private $menu;

    /**
     * @var string $display_type
     *
     * @ORM\Column(name="display_type", type="string", length=255)
     */
    private $display_type;

    public function __construct()
    {
        $bloc = new Bloc();
        $this->setBloc($bloc);
    }

    public function displayBloc($options = null)
    {
        $html = '';

        $menus = $this->menu->getMenus();
        switch ($this->display_type) {
            case 'footer':
                if (!empty($menus)) {
                    $html .= '<ul class="unstyled menu-footer">';
                    foreach ($menus as $entry) {
                        if ($entry->getLevel() == 1) {
                            $html .= '<li><a href="'.$entry->getUrl().'">'.$entry->getTitle().'</a></li>';
                        }
                    }
                    $html .= '</ul>';
                }
                break;
            case 'header':
                $old_level = 1;
                $first = true;
                $id_old = 0;
                if (!empty($menus)) {
                    $html .= '<ul class="unstyled menu-header horizontal">';
                    foreach ($menus as $entry) {
                        if($entry->getRoot() == $entry->getId())
                            continue;

                        switch ($entry->getLevel()) {
                            case 1:
                                switch ($old_level) {
                                    case 1:
                                        if (!$first) {
                                            $html .= '</li>';
                                        }
                                        $html .= '<li class="dropdown">';
                                        $html .= '<a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown" href="/'.substr($entry->getLanguage()->getIso(), 0, 2).'/'.$entry->getUrl().'">'.$entry->getTitle().'</a>';
                                        $first = false;
                                        break;
                                    case 2:
                                        $html .= '</li></ul></li>';
                                        $html .= '<li class="dropdown"><a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown"  href="/'.substr($entry->getLanguage()->getIso(), 0, 2).'/'.$entry->getUrl().'">'.$entry->getTitle().'</a>';
                                        break;
                                }
                                $id_old = $entry->getId();
                                break;
                            case 2:
                                switch ($old_level) {
                                    case 1:
                                        $html .= '<ul class="dropdown-menu" aria-labelledby="drop-'.$id_old.'" role="menu"><li><a href="/'.substr($entry->getLanguage()->getIso(), 0, 2).'/'.$entry->getUrl().'">'.$entry->getTitle().'</a>';
                                        break;
                                    case 2:
                                        $html .= '</li><li><a href="/'.substr($entry->getLanguage()->getIso(), 0, 2).'/'.$entry->getUrl().'">'.$entry->getTitle().'</a>';
                                        break;
                                }
                                break;
                        }
                        //  echo $old_level.' - '.$entry->getLevel().'<br />';
                        $old_level = $entry->getLevel();

                    }
                    $html .= '</ul>';
                }
                break;
            case 'admin':
                $old_level = 0;
                $first = true;
                $id_old = 0;
                if (!empty($menus)) {
                    $html .= '<ul class="nav">';
                    foreach ($menus as $entry) {
                        //echo $entry->getLevel().' '.$old_level.'<br />'; 

                        switch ($entry->getLevel()) {
                            case 0:
                                switch ($old_level) {
                                    case 0:
                                        if (!$first) {
                                            $html .= '</li>';
                                        }
                                        $html .= '<li class="dropdown">';
                                        $html .= '<a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown" href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                        $first = false;
                                        break;
                                    case 1:
                                        $html .= '</li></ul></li>';
                                        $html .= '<li class="dropdown"><a id="drop-'.$entry->getId().'" class="dropdown-toggle" data-toggle="dropdown"  href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                        break;
                                }
                                $id_old = $entry->getId();
                                break;
                            case 1:
                                switch ($old_level) {
                                    case 0:
                                        $html .= '<ul class="dropdown-menu" aria-labelledby="drop-'.$id_old.'" role="menu"><li><a href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                        break;
                                    case 1:
                                        $html .= '</li><li><a href="'.$entry->getNameRoute().'">'.$entry->getTitle().'</a>';
                                        break;
                                }
                                break;
                        }
                        //  echo $old_level.' - '.$entry->getLevel().'<br />';
                        $old_level = $entry->getLevel();

                    }
                    $html .= '</ul>';
                }
                break;

        }

        return $html;
    }

    public function getType()
    {
        return 'BlocMenu';
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
     * @param CMS\BlocBundle\Entity\Bloc $bloc
     */
    public function setBloc(\CMS\BlocBundle\Entity\Bloc $bloc)
    {
        $this->bloc = $bloc;
    }

    /**
     * Get bloc
     *
     * @return CAF\BlocBundle\Entity\Bloc
     */
    public function getBloc()
    {
        return $this->bloc;
    }

    /**
     * Set display_type
     *
     * @param string $displayType
     */
    public function setDisplayType($displayType)
    {
        $this->display_type = $displayType;
    }

    /**
     * Get display_type
     *
     * @return string
     */
    public function getDisplayType()
    {
        return $this->display_type;
    }

    /**
     * Set menu
     *
     * @param CMS\MenuBundle\Entity\MenuTaxonomy $menu
     */
    public function setMenu(\CMS\MenuBundle\Entity\MenuTaxonomy $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Get menu
     *
     * @return CMS\MenuBundle\Entity\MenuTaxonomy
     */
    public function getMenu()
    {
        return $this->menu;
    }

}