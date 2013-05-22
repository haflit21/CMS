<?php
namespace CMS\ContentBundle\Type\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CMS\ContentBundle\Entity\CMTag;

class TagToStringTransformer implements DataTransformerInterface
{

	/**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (tag) to a string (name).
     *
     * @param  Tags|null $tag
     * @return string
     */
    public function transform($tags)
    {
        if (null === $tags) {
            return "";
        }
        $str = '';
        foreach($tags as $tag)
        	$str .= $tag->getTitle().',';
        return $str;
    }

    /**
     * Transforms a string (title) to an object (tag).
     *
     * @param  string $title
     *
     * @return Tag|null
     *
     */
    public function reverseTransform($title)
    {
        if (!$title) {
            return null;
        }

        $tag = $this->om
            ->getRepository('CMSContentBundle:CMTag')
            ->findOneBy(array('title' => $title))
        ;

        //var_dump($title); die;

        if (null === $tag) {
            $tag = new CMTag();
            $tag->setTitle($title);
        }

        return $tag;
    }
}