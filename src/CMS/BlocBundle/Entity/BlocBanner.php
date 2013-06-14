<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAF\BlocBundle\Entity\BlocMenu
 *
 * @ORM\Table(name="bloc_banner")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocBanner")
 */
class BlocBanner
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
     * @var text $images
     *
     * @ORM\Column(name="images", type="text")
     */
    private $images;

    /**
     * @var text $slogans
     *
     * @ORM\Column(name="slogans", type="text")
     **/
    private $slogans;

    public function displayBloc($options = null)
    {
        $str = '<div class="row-responsive bg-slider visible-tablet  visible-desktop">';
        $str .= '<div class="container">';
        $str .= '<div id="slider">';
        $i=1;
        $j=0;
        $slogans = $this->getSlogans();
        foreach ($this->getImages() as $image) {
            $k=1;
            $str .= '<div class="ls-layer">';
            $str .= '<img class="ls-bg" src="'.$image.'" alt="layer1-background">';
            $str .= '<div class="ls-s'.$k.'">'.$slogans[$j][0].'</div>';
            $i++;
            $k++;
            $str .= '<div class="ls-s'.$k.'">'.$slogans[$j][1].'</div>';
            $str .= '</div>';
            $j++;

        }
        $str .= '<div class="clearfix"></div>';
        $str .= '</div>';
        $str .= '</div>';
        $str .= '</div>';
        $str .= '<div class="clearfix"></div>';

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
     * Set images
     *
     * @param  string     $images
     * @return BlocBanner
     */
    public function setImages($images)
    {
        $this->images = serialize($images);

        return $this;
    }

    /**
     * Get images
     *
     * @return string
     */
    public function getImages()
    {
        $images = unserialize($this->images);

        return explode('|',$images['image']);
    }

    /**
     * Set bloc
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $bloc
     * @return BlocBanner
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

    /**
     * Set slogans
     *
     * @param  string     $slogans
     * @return BlocBanner
     */
    public function setSlogans($slogans)
    {
        $this->slogans = $slogans;

        return $this;
    }

    /**
     * Get slogans
     *
     * @return string
     */
    public function getSlogans()
    {
        $slogans = explode('%%', $this->slogans);
        $i=0;
        foreach ($slogans as $slogan) {
            $lines = explode('::',$slogan);
            $slogans[$i] = $lines;
            $i++;
        }

        return $slogans;
    }

    public function getType()
    {
        return 'BlocBanner';
    }
}