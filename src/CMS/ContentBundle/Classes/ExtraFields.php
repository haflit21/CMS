<?php

namespace CMS\ContentBundle\Classes;

use CMS\ContentBundle\Entity\CMFieldValue;

class ExtraFields
{
    public static function loadFields($controller, $contenttype)
    {
        $html = null;

        if ($contenttype) {
            $type = $controller->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->find($contenttype);
            foreach ($type->getFields() as $key => $field) {
                if ($field->getPublished()) {
                    $html .= $field->getField()->displayfield($field,null);
                }
            }
        }

        return $html;
    }

    public static function saveFields($controller, $em, $request, $content, $contenttype)
    {
        if ($contenttype) {
            $type = $controller->getDoctrine()->getRepository('CMSContentBundle:CMContentType')->find($contenttype);
            $content->setContentType($type);
            foreach ($type->getFields() as $key => $field) {
                $value = $request->request->get($field->getName());
                $fieldvalue = new CMFieldValue;
                $fieldvalue->setValue($value);
                $fieldvalue->setContent($content);
                $fieldvalue->setField($field);
                $content->addFieldValue($fieldvalue);
                $field->addFieldValue($fieldvalue);
                $em->persist($fieldvalue);
                $em->persist($field);
            }
        }

        return true;
    }
}
