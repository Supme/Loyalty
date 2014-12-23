<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

/**
 * Class Form
 *
 *  Form->field(array(
 * 'label'       => "Full Name",
 * 'name'        => "name",
 * ))
 *
 * Form::field(array(
 * 'label'       => "Email Address",
 * 'name'        => "email",
 * 'input-prepend' => '<i class="icon-envelope"></i>',
 * 'input-attributes' => array('placeholder' => "example@example.com"),
 * 'input-class' => "input-large"
 * ))
 *
 */
class Form {

    private $settings = [];
    private $legend = false;
    private $fields = '';

    /**
     * @param array $options
     */
    function __construct($options = [])
    {
        //Assign Defaults
        $defaults = array(
            'action' => "",
            'name' => "",
            'id' => "",
            'class' => "",
            'method' => "POST",
        );
        //Overwrite Defaults with $options
        $this->settings = array_merge($defaults,$options);
    }

    public function render()
    {
        $settings_items = array();
        foreach($this->settings as $settings_name => $settings_value){
            $settings_items[] = $settings_name.'="'.$settings_value.'"';
        };
        $settings_string = implode(' ',$settings_items);
        $form =  '<form '.$settings_string.'>';
        $form .= '<fieldset>';
        if($this->legend)
            $form .= '<legend>'.$this->legend.'</legend>';
        $form .= $this->fields;
        $form .= '</fieldset></form>';

        return $form;
    }

    /**
     * @param string $legend
     */
    public function legend($legend)
    {
        $this->legend = $legend;
    }

    /**
     * Generate an html form field component
     *
     * @param  array  $options
     * @return string
     */
    public function field($options)
    {
        //Assign Defaults
        $defaults = array(
            'type' => "text", //field input type (text, textarea, password, select, checkbox, radio, file)
            'name' => "", //field input name
            'id' => "", //field input id
            'value' => "", //field input value
            'label' => "", //field label
            'help' => "", //field help display text
            'label-class' => "", //label class
            'group' => true, //wrap group
            'group-class' => "", //class applied to the group container
            'group-attributes' => array(), //additional group attributes
            'controls' => true, //wrap controls
            'controls-class' => "", //class applied to the controls container
            'controls-attributes' => array(), //additional controls attributes
            'input-class' => "", //class applied to the input
            'input-attributes' => array(), //additional input attributes
            'input-prepend' => "", //prepend control data
            'input-append' => "", //append control data
            'base-label-class' => "control-label", //initial label class (in addition to label-class)
            'base-group-class' => "form-group", //initial group class (in addition to group-class)
            'base-controls-class' => "controls", //initial controls class (in addition to controls-class)
            'base-input-class' => "form-control",
        );
        //Overwrite Defaults with $options
        $field = array_merge($defaults,$options);
        //Some field updates
        //--create id if not set
        if(empty($field['id'])){
            $field['id'] = $field['name'];
        }
        //--Add field type to group class
        $field['group-class'] .= ' field-group-'.$field['type'];
        //--Add name to group class
        $field['group-class'] .= ' '.$field['base-group-class'].'-'.$this->cleanString($field['name']);
        //Create output
        $output_items = array();
        //Create field HTML
        $output_items['field'] = $this->makeField($field);
        $output_items['label'] = $this->makeLabel($field);
        //Wrap Controls
        $output_items['controls'] = $this->wrapControls($field, $output_items['field']);
        //Wrap Group
        $output_items['group'] = $this->wrapGroup($field, $output_items['label'].$output_items['controls']);

        $output = $output_items['group'];

        $this->fields = $this->fields."\n".$output;
    }

    /**
     * Make field HTML from field array
     *
     * @param  array  $field
     * @return string
     */
    private function makeField($field)
    {
        $output = '';
        //combine additional input attributes
        $input_attributes = array('class'=>$field['base-input-class'].' '.$field['input-class']);
        $input_attributes = array_merge($input_attributes, $field['input-attributes']);
        $attributes_string_items = array();
        foreach($input_attributes as $input_attribute_name => $input_attribute_value){
            $attributes_string_items[] = $input_attribute_name.'="'.$input_attribute_value.'"';
        }
        $attributes_string = implode(' ',$attributes_string_items);

        //TEXT
        if($field['type'] == "text"){
            $output .= '<input type="text" name="'.$field['name'].'" value="'.$field['value'].'" '.$attributes_string.' />';
        }

        //TEXTAREA
        if($field['type'] == "textarea"){
            $output .= '<input type="textarea" name="'.$field['name'].'" value="'.$field['value'].'" '.$attributes_string.' />';
        }

        //PASSWORD
        if($field['type'] == "password"){
            $output .= '<input type="password" name="'.$field['name'].'" value="'.$field['value'].'" '.$attributes_string.' />';
        }

        //SELECT
        if($field['type'] == "select"){
            $output .= '<select name="'.$field['name'].'"  '.$attributes_string.'>';
            foreach($field['options'] as $opk=>$option){
                $selected = '';
                if($opk == $field['value']){
                    $selected = 'selected = "selected"';
                }
                $output .= '<option value="'.$opk.'" '.$selected.'>'.$option.'</option>';
            }
            $output .= '</select>';
        }

        //CHECKBOXES
        if($field['type'] == "checkbox"){
            foreach($field['options'] as $option_key=>$option_value){
                $output .= '<label class="checkbox">';
                $checked = null;
                if(is_array($field['value']) && in_array($option_key, $field['value'])){
                    $checked = true;
                }
                $output .= Form::checkbox($field['name'], $option_key, $checked);
                $output .= $option_value;
                $output .= '</label>';
            }
        }

        //RADIO
        if($field['type'] == "radio"){
            foreach($field['options'] as $option_key=>$option_value){
                $output .= '<label class="radio">';
                $checked = false;
                if($option_key == $field['value']){
                    $checked = true;
                }
                $output .= Form::radio($field['name'], $option_key, $checked);
                $output .= $option_value;
                $output .= '</label>';
            }
        }

        //FILE
        if($field['type'] == "file"){
            $output .= Form::file($field['name'],$input_attributes);
        }

        //SUBMIT
        if($field['type'] == "submit"){
            $output .= '<button type="submit" class="btn btn-default" '.$attributes_string.'>'.$field['value'].'</button>';
        }

        return $output;
    }

    /**
     * Make field label from field array
     *
     * @param  array  $field
     * @return string
     */
    private function makeLabel($field){
        $output = '';
        if(!empty($field['label'])){
            //Label Start
            $output .= '<label for="'.$field['name'].'" class="'.$field['base-label-class'].' '.$field['label-class'].'">';
            $output .= $field['label'];
            //Label End
            $output .= '</label>';
        }
        return $output;
    }

    /**
     * Wrap field in control html
     *
     * @param  array  $field
     * @param  string  $contents
     * @return string
     */
    private function wrapControls($field,$contents)
    {
        $control_attributes = $this->makeAttributes($field['controls-attributes']);
        if($field['controls']){
            //Add append / prepend classes
            if(!empty($field['input-prepend'])){
                $field['controls-class'] .= ' input-prepend';
            }
            if(!empty($field['input-append'])){
                $field['controls-class'] .= ' input-append';
            }
            //
            $controls_start = '';
            $controls_end = '';
            //start
            $controls_start = '<div class="'.$field['base-controls-class'].' '.$field['controls-class'].'" '.$control_attributes.'>';
            //prepend
            if(!empty($field['input-prepend'])){
                $controls_start .= '<span class="add-on">'.$field['input-prepend'].'</span>';
            }
            //append
            if(!empty($field['input-append'])){
                $controls_end .= '<span class="add-on">'.$field['input-append'].'</span>';
            }
            //HELP
            if(!empty($field['help'])){
                $controls_end .= '<p class="help-block">'.$field['help'].'</p>';
            }
            //Controls End
            $controls_end .= '</div>';
            //output
            return $controls_start.$contents.$controls_end;
        }else{
            return $contents;
        }
    }

    /**
     * Wrap field in group html
     *
     * @param  array  $field
     * @param  string  $contents
     * @return string
     */
    private function wrapGroup($field,$contents)
    {
        $group_attributes = $this->makeAttributes($field['group-attributes']);
        if($field['controls']){
            $group_start = '';
            $group_end = '';
            $group_start .= '<div class="'.$field['base-group-class'].' '.$field['group-class'].'"  '.$group_attributes.'>';
            $group_end .= '</div>';
            return $group_start.$contents.$group_end;
        }else{
            return $contents;
        }
    }

    /**
     * Make attributes html from array
     *
     * @param  array  $attributes
     * @return string
     */
    private function makeAttributes($attributes)
    {
        $output = '';
        $attr_items = array();
        if(is_array($attributes)){
            foreach($attributes as $attributes_key => $attributes_value){
                $attr_items[] = $attributes_key.' = "'.$attributes_value.'"';
            }
            $output = implode(' ',$attr_items);
        }
        return $output;
    }

    private function cleanString($string)
    {
        $string = preg_replace("/[^A-Za-z0-9]/", '', $string);
        return $string;
    }


} 