<?php

namespace CMS\BlocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CMS\BlocBundle\Entity\Bloc;

/**
 * CMS\ContentBundle\Entity\Content
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CMS\ContentBundle\Entity\Repository\ContentRepository")
 */
class BlocHtml
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
     * @var text $code_html
     *
     * @ORM\Column(name="code_html", type="text", nullable=true)
     */
    private $code_html;

    public function displayBloc()
    {
        return $this->code_html;
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
     * Set code_html
     *
     * @param  string   $codeHtml
     * @return BlocHtml
     */
    public function setCodeHtml($codeHtml)
    {
        $this->code_html = $codeHtml;

        return $this;
    }

    /**
     * Get code_html
     *
     * @return string
     */
    public function getCodeHtml()
    {
        return $this->code_html;
    }

    /**
     * Set bloc
     *
     * @param  \CMS\BlocBundle\Entity\Bloc $bloc
     * @return BlocHtml
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
}