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

        $tags = explode(',',$title);

        $tags_result = new \Doctrine\Common\Collections\ArrayCollection();

        $tags_source = $this->om->getRepository('CMSContentBundle:CMTag')->getAllTagsTitle();
        $tags_objects = $this->om->getRepository('CMSContentBundle:CMTag')->getAllTagsTitleObject();

        foreach ($tags as $tag) {
        	$tag = trim($tag);
            if (in_array($tag, $tags_source)) {
                $tag_current = $tags_objects[$tag];
                $tags_result[] = $tag_current;
            } else if($tag != '') {
                $tag_current = new CMTag;
                $tag_current->setTitle($tag);
                $tags_result[] = $tag_current;
                $this->om->persist($tag_current);
            }
        }
        return $tags_result;
    }
}