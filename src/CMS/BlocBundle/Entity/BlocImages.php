<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAF\BlocBundle\Entity\BlocMenu
 *
 * @ORM\Table(name="bloc_images")
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocImage")
 */
class BlocImages
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

    public function displayBloc()
    {
        $html = '<div class="col4"><ul class="unstyled">';
        foreach ($this->getImages() as $image) {
            $html .= '<li class="thumbnail"><img src="'.$image.'" /></li>';
        }
        $html .= '</ul></div>';

        return $html;
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
     * @return BlocImages
     */
    public function setImages($images)
    {
        $this->images = serialize($images);

        return $this;
    }

    /**
     * Get images
     *
     * @return array
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
     * @return BlocImages
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
        return 'BlocImages';
    }
}