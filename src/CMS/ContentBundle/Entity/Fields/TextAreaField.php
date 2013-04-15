<?php

namespace CMS\ContentBundle\Entity\Fields;

use CMS\ContentBundle\Classes\Fields;

/**
 * CMS\ContentBundle\Entity\Fields\TextAreaField
 *
 */
class TextAreaField extends Fields
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
        return 'textarea';
    }

    public function getName()
    {
        return 'Textarea field type';
    }

    public function getClassName()
    {
        return 'TextAreaField';
    }

    public function displayfield($field, $value=null)
    {
        $useeditor = $this->params['useeditor'];
        $html = '<div class="control-group"><div class="control-label">'.$field->getTitle().'</div>';
        $name = $field->getName();
        /*if ($useeditor) {
            $name = "ckeditor_".$field->getName();
            $html .= '
                    <script type="text/javascript" src="/ContentBundle/web/bundles/trsteelckeditor/ckeditor.js"></script>
                    <script type="text/javascript">
                        CKEDITOR.replace("'.$name.'",{width: "100%",height: "320",language: "en-au",uiColor: "#fff",toolbar: [{"name":"document","items":["Source","-","Save","-","Templates"]},{"name":"basicstyles","items":["Bold","Italic","Underline","Strike","Subscript","Superscript","-","RemoveFormat"]}]});
                    </script>
                    ';
        }*/
        $html .= '<div class="controls"><textarea id="'.$name.'" name="'.$name.'" rows="'.$this->getParamsValue($this->params, 'rows').'" cols="'.$this->getParamsValue($this->params, 'cols').'">'.$value.'</textarea></div></div>';

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
            <label for="cols">Colonne</label>
            <input type="text" id="cols" name="options[cols]" value="'.$this->getParamsValue($this->params, 'cols').'" />

            <label for="rows">Ligne</label>
            <input type="text" id="rows" name="options[rows]" value="'.$this->getParamsValue($this->params, 'rows').'" />

            <label for="defaultvalue">Default Value</label>
            <input type="text" id="defaultvalue" name="options[defaultvalue]" value="'.$this->getParamsValue($this->params, 'defaultvalue').'" />

            <label for="required">Required</label>
            <select id="required" name="options[required]" >
                <option '.$this->getParamsValue($this->params, 'required', 'select', 0).' value="0">No</option>
                <option '.$this->getParamsValue($this->params, 'required', 'select', 1).' value="1">Yes</option>
            </select>

            <label for="useeditor">Use Editor</label>
            <select id="useeditor" name="options[useeditor]" >
                <option '.$this->getParamsValue($this->params, 'useeditor', 'select', 0).' value="0">No</option>
                <option '.$this->getParamsValue($this->params, 'useeditor', 'select', 1).' value="1">Yes</option>
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
