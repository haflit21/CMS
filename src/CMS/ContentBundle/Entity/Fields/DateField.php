<?php

namespace CMS\ContentBundle\Entity\Fields;

use CMS\ContentBundle\Classes\Fields;

/**
 * CMS\ContentBundle\Entity\Fields\TextField
 *
 */
class DateField extends Fields
{
    private $html;

    private $type;

    private $params;

    public function __construct()
    {
        $this->type = $this->getTypeField();
        $this->params = array();
    }

    public function getTypeField()
    {
        return 'Date';
    }

    public function getName()
    {
        return 'Date field type';
    }

    public function getClassName()
    {
        return 'DateField';
    }

    public function displayfield($field, $value=null)
    {
        $html = '<div class="control-group"><div class="control-label">'.$field->getTitle().'</div>';
        $html .= '<div class="controls"><input type="text" name="'.$field->getName().'" value="'.$value.'" size="'.$this->getParamsValue($this->params, 'size').'" class="datepicker-field" /></div></div>';

        return $html;
    }

    public function display($value)
    {
        $start = null;
        $end = null;

        switch ($this->params['displaytype']) {
            case 'div':
                $start = '<div>';
                $end = '</div>';
            break;
            case 'span':
                $start = '<span>';
                $end = '</span>';
            break;
            case 'p':
                $start = '<p>';
                $end = '</p>';
            break;
        }

        $html = $start.' '.$value.' '.$end;

        return $html;
    }

    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    // faire une classe avec les display des differents types: checkbox, select, input etc.
    public function getOptions()
    {
        $options = '
            <label for="size">Size</label>
            <input type="text" id="size" name="options[size]" value="'.$this->getParamsValue($this->params, 'size').'" />
            <label for="defaultvalue">Default Value</label>
            <input type="text" id="defaultvalue" name="options[defaultvalue]" value="'.$this->getParamsValue($this->params, 'defaultvalue').'" />
            <label for="required">Required</label>
            <select id="required" name="options[required]" >
                <option '.$this->getParamsValue($this->params, 'required', 'select', 0).' value="0">No</option>
                <option '.$this->getParamsValue($this->params, 'required', 'select', 1).' value="1">Yes</option>
            </select>
            <label for="displaytype">Display in </label>
            <select if="displaytype" name="options[displaytype]" >
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'div').' value ="div">div</option>
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'span').' value ="span">span</option>
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'p').' value ="p">paragraphe</option>
            </select>
            <label for="format">Format de la date </label>
            <input type="text" name="options[format]" id="format" placeholder="%d/%m/%Y" />
        ';

        return $options;
    }
}
