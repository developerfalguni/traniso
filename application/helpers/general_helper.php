<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

////////////// Remove All the special Char from string
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function getFilterable() {
	return array(
		"1" 	=> 'Is equal to',
        "2"		=> 'Is not equal to',
        "3"		=> 'Starts with',
        "4"		=> 'Contains',
        "5"		=> 'Does not contain',
        "6"		=> 'Ends with',
        /*"7"		=> 'Is null',
        "8"		=> 'Is not null',
        "9"		=> 'Is empty',
        "10"	=> 'Is not empty',
        "11"	=> 'Has Value',
        "12"	=> 'Has no Value',*/
	);
}

function form_dropdown_single($data = '', $options = array(), $selected = array(), $extra = '')
{
	$defaults = array();

	if (is_array($data))
	{
		if (isset($data['selected']))
		{
			$selected = $data['selected'];
			unset($data['selected']); // select tags don't have a selected attribute
		}

		if (isset($data['options']))
		{
			$options = $data['options'];
			unset($data['options']); // select tags don't use an options attribute
		}
	}
	else
	{
		$defaults = array('name' => $data);
	}

	is_array($selected) OR $selected = array($selected);
	is_array($options) OR $options = array($options);

	// If no selected state was submitted we will attempt to set it automatically
	if (empty($selected))
	{
		if (is_array($data))
		{
			if (isset($data['name'], $_POST[$data['name']]))
			{
				$selected = array($_POST[$data['name']]);
			}
		}
		elseif (isset($_POST[$data]))
		{
			$selected = array($_POST[$data]);
		}
	}

	$extra = _attributes_to_string($extra);

	$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

	$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">";

	foreach ($options as $key => $val)
	{
		$key = (string) $key;

		if (is_array($val))
		{
			if (empty($val))
			{
				continue;
			}

			$form .= '<optgroup label="'.$key."\">";

			foreach ($val as $optgroup_key => $optgroup_val)
			{
				$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
				$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
					.(string) $optgroup_val."</option>";
			}

			$form .= "</optgroup>";
		}
		else
		{
			$form .= '<option value="'.html_escape($key).'"'
				.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
				.(string) $val."</option>";
		}
	}

	return $form."</select>";
}

function form_checkbox_custom($data = '', $value = '', $checked = FALSE, $extra = '')
{
	$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

	if (is_array($data) && array_key_exists('checked', $data))
	{
		$checked = $data['checked'];

		if ($checked == FALSE)
		{
			unset($data['checked']);
		}
		else
		{
			$data['checked'] = 'checked';
		}
	}

	if ($checked == TRUE)
	{
		$defaults['checked'] = 'checked';
	}
	else
	{
		unset($defaults['checked']);
	}

	return '<input '._parse_form_attributes_custom($data, $defaults)._attributes_to_string_custom($extra)." />";
}

function _parse_form_attributes_custom($attributes, $default)
{
	if (is_array($attributes))
	{
		foreach ($default as $key => $val)
		{
			if (isset($attributes[$key]))
			{
				$default[$key] = $attributes[$key];
				unset($attributes[$key]);
			}
		}

		if (count($attributes) > 0)
		{
			$default = array_merge($default, $attributes);
		}
	}

	$att = '';

	foreach ($default as $key => $val)
	{
		if ($key === 'value')
		{
			$val = html_escape($val);
		}
		elseif ($key === 'name' && ! strlen($default['name']))
		{
			continue;
		}

		$att .= $key.'=\''.$val.'\' ';
	}

	return $att;
}

function _attributes_to_string_custom($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'=\''.$val.'\'';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
