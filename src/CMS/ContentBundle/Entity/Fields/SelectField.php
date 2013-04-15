<?php

namespace CMS\ContentBundle\Entity\Fields;

use CMS\ContentBundle\Classes\Fields;

/**
 * CMS\ContentBundle\Entity\Fields\SelectField
 *
 */
class SelectField extends Fields
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
        return 'select';
    }

    public function getName()
    {
        return 'Select field type';
    }

    public function getClassName()
    {
        return 'SelectField';
    }

    public function displayfield($field, $value=null)
    {
        $values = $this->getOptionsSelect($value);
        $options = $this->params['options'];
        $html = '<div class="control-group"><div class="control-label">'.$field->getTitle().'</div>';
        if ($options['multiple']) {
            $html .= '<div class="controls"><select name="'.$field->getName().'[]" multiple>'.$values.'</select></div></div>';
        } else {
            $html .= '<div class="controls"><select name="'.$field->getName().'" '.$multiple.'>'.$values.'</select></div></div>';
        }

        return $html;
    }

    private function getOptionsSelect($value)
    {
        $options = $this->params['options'];
        $multiple = is_array($value);
        $options = explode('%%', $options);
        $html = '';
        foreach ($options as $key => $option) {
            $option = explode('::', trim($option));
            if ($multiple) {
                $isselected = (in_array($option[0],$value))?'selected="selected"':'';
            } else {
                $isselected = ($option[0]==$value)?'selected="selected"':'';
            }
            $html .= '<option value="'.$option[0].'" '.$isselected.'>'.$option[1].'</option>';
        }

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
            <label for="options">Options</label>
            <textarea id="options" name="options[options]" >'.$this->getParamsValue($this->params, 'options').'</textarea>
            <label for="required">Required</label>
            <select id="required" name="options[required]" >
                <option '.$this->getParamsValue($this->params, 'required', 'select', 0).' value="0">No</option>
                <option '.$this->getParamsValue($this->params, 'required', 'select', 1).' value="1">Yes</option>
            </select>
            <label for="multiple">Multiple</label>
            <select id="multiple" name="options[multiple]" >
                <option '.$this->getParamsValue($this->params, 'multiple', 'select', 0).' value="0">No</option>
                <option '.$this->getParamsValue($this->params, 'multiple', 'select', 1).' value="1">Yes</option>
            </select>
            <label for="displaytype">Display in </label>
            <select if="displaytype" name="options[displaytype]" >
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'div').' value ="div">div</option>
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'span').' value ="span">span</option>
                <option '.$this->getParamsValue($this->params, 'displaytype', 'select', 'p').' value ="p">paragraphe</option>
            </select>
        ';

        return $options;
    }
}
