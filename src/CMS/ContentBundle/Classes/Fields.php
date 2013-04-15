<?php

namespace CMS\ContentBundle\Classes;

class Fields
{
    public function getParamsValue($params, $name, $type="default", $option=null)
    {
        switch ($type) {
            case 'default':
                if (array_key_exists($name, $params)) {
                    return $params[$name];
                } else {
                    return '';
                }
                break;
            case 'select':
                if (array_key_exists($name, $params)) {
                    if($option == $params[$name])

                        return ' selected="selected" ';
                    else
                        return '';
                } else {
                    return '';
                }
                break;
        }
    }
}
