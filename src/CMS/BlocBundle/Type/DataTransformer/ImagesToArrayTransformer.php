<?php
namespace CMS\BlocBundle\Type\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ImagesToArrayTransformer implements DataTransformerInterface
{

    private $fields;

    /**
     * Constructor.
     *
     * @param array $fields The date fields
     *
     * @throws UnexpectedTypeException if an image is not a string
     */
    public function __construct(array $fields = null)
    {

        if (null === $fields) {
            $fields = array('images');
        }

        $this->fields = $fields;
    }

     /**
     * Transforms a normalized date into a localized date.
     *
     * @param DateTime $dateTime Normalized date.
     *
     * @return array Localized date.
     *
     * @throws UnexpectedTypeException       if the given value is not an instance of \DateTime
     * @throws TransformationFailedException if the output timezone is not supported
     */
    public function transform($array)
    {
        if (null === $array) {
            return array_intersect_key(array(
                'images'     => array()
            ), array_flip($this->fields));
        }

        if (!is_array($array)) {
            throw new UnexpectedTypeException($array, 'Array');
        }

        $result = array_intersect_key(array(
            'images'     => $array['images']
        ), array_flip($this->fields));

        return $result;
    }

    /**
     * Transforms a localized date into a normalized date.
     *
     * @param array $value Localized date
     *
     * @return DateTime Normalized date
     *
     * @throws UnexpectedTypeException       if the given value is not an array
     * @throws TransformationFailedException if the value could not bet transformed
     * @throws TransformationFailedException if the input timezone is not supported
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (implode('', $value) === '') {
            return null;
        }

        $emptyFields = array();

        foreach ($this->fields as $field) {
            if (!isset($value[$field])) {
                $emptyFields[] = $field;
            }
        }

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(
                sprintf('The fields "%s" should not be empty', implode('", "', $emptyFields)
            ));
        }

        try {
            if (!is_array($value)) {
                $string = unserialize($value);
            } else {
                $string = $value;
            }

        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $string;
    }
}
