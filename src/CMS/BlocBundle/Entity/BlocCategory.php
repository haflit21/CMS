<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\BlocBundle\Entity\Bloc;

/**
 * CMS\ContentBundle\Entity\Content
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CMS\BlocBundle\Entity\Repository\BlocCategoryRepository")
 */
class BlocCategory
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
    * @ORM\ManyToOne(targetEntity="\CMS\BlocBundle\Entity\Bloc")
    */
    private $bloc;

     /**
     * @var entity $category
     *
     * @ORM\OneToOne(targetEntity="\CMS\ContentBundle\Entity\CMCategory")
     */
    private $category;

    /**
     * @var int $nbArticles
     *
     * @ORM\Column(name="nbArticles", type="integer")
     **/
    private $nbArticles;

    /**
     * @var int $fieldOrder
     *
     * @ORM\OneToOne(targetEntity="\CMS\ContentBundle\Entity\CMField")
     **/
     private $fieldOrder;

    public function displayBloc($options = null)
    {
        $str = '<h3>'.$this->bloc->getTitle().'</h3>';
        $articles = $this->getCategory()->getContents();
        $i=0;
        foreach ($articles as $article) {
            if ($i < $this->getNbArticles()) {
                $str .= '<h4><a href="'.$article->getUrl().'">'.$article->getTitle().'</a></h4>';
                $str .= '<p>'.substr($article->getDescription(),0,50).'...</p>';
                $str .= '<div class="clearfix"></div>';
            }
            $i++;
        }

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
     * Set bloc
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $bloc
     * @return BlocCategory
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
     * Set category
     *
     * @param  \CMS\ContentBundle\Entity\CMCategory $category
     * @return BlocCategory
     */
    public function setCategory(\CMS\ContentBundle\Entity\CMCategory $category = null)
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
     * Set fieldOrder
     *
     * @param  \CMS\ContentBundle\Entity\CMField $fieldOrder
     * @return BlocCategory
     */
    public function setFieldOrder(\CMS\ContentBundle\Entity\CMField $fieldOrder = null)
    {
        $this->fieldOrder = $fieldOrder;

        return $this;
    }

    /**
     * Get fieldOrder
     *
     * @return \CMS\ContentBundle\Entity\CMField
     */
    public function getFieldOrder()
    {
        return $this->fieldOrder;
    }

    public function getType()
    {
        return 'BlocCategory';
    }

    /**
     * Set nbArticles
     *
     * @param  integer      $nbArticles
     * @return BlocCategory
     */
    public function setNbArticles($nbArticles)
    {
        $this->nbArticles = $nbArticles;

        return $this;
    }

    /**
     * Get nbArticles
     *
     * @return integer
     */
    public function getNbArticles()
    {
        return $this->nbArticles;
    }
}