<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function site_url($uri = '')
{
    $CI =& get_instance();
    if(substr($uri, 0, 7) == 'http://' || substr($uri, 0, 8) == 'https://') return $uri;
    $iframe_ul_suffix = '?' . cfg('url', 'iframe') . '=' . cfg('form', 'true');
    if($CI->input->get(cfg('url', 'iframe')) == cfg('form', 'true') && substr($uri, -(strlen($iframe_ul_suffix))) != $iframe_ul_suffix) $uri .= $iframe_ul_suffix;
    return $CI->config->site_url($uri);
}

function set_multiple($field = '', $default = array())
{
    if(is_array(@$_REQUEST[$field]))
    {
        return (array)$_REQUEST[$field];
    }
    else
    {
        return $default;
    }
}

function set_value($field = '', $default = '')
{
    if(FALSE === ($OBJ =& _get_validation_object()))
    {
        if(!isset($_POST[$field]))
        {
            return $default;
        }

        return form_prep($_POST[$field], $field);
    }

    $field_value = form_prep($OBJ->set_value($field, ''), $field);
    
    if(strlen($field_value) == 0)
    {
        $value = get_from_array($_POST, $field);
        return (isset($value)) ? $value : $default;
        
        //return ($value === FALSE) ? ((isset($_POST[$field])) ? $_POST[$field] : $default) : $value;
    }
    else
    {
        return $field_value;
    }
}

function get_from_array($array = NULL, $key = '')
{
    foreach(explode('[', str_replace(']', '', $key)) as $key)
    {
        if(is_array($array)) $array = @$array[$key];
    }
    
    return $array;
}

function url_title($str, $separator = 'dash', $lowercase = TRUE)
{
    $is_some_input = (strlen($str) > 0);
    
    $foreign_characters = array(
	'ä|æ|ǽ' => 'ae',
	'ö|œ' => 'oe',
	'ü' => 'ue',
	'Ä' => 'Ae',
	'Ü' => 'Ue',
	'Ö' => 'Oe',
	'À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ' => 'A',
	'à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª' => 'a',
	'Ç|Ć|Ĉ|Ċ|Č' => 'C',
	'ç|ć|ĉ|ċ|č' => 'c',
	'Ð|Ď|Đ' => 'D',
	'ð|ď|đ' => 'd',
	'È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě' => 'E',
	'è|é|ê|ë|ē|ĕ|ė|ę|ě' => 'e',
	'Ĝ|Ğ|Ġ|Ģ' => 'G',
	'ĝ|ğ|ġ|ģ' => 'g',
	'Ĥ|Ħ' => 'H',
	'ĥ|ħ' => 'h',
	'Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ' => 'I',
	'ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı' => 'i',
	'Ĵ' => 'J',
	'ĵ' => 'j',
	'Ķ' => 'K',
	'ķ' => 'k',
	'Ĺ|Ļ|Ľ|Ŀ|Ł' => 'L',
	'ĺ|ļ|ľ|ŀ|ł' => 'l',
	'Ñ|Ń|Ņ|Ň' => 'N',
	'ñ|ń|ņ|ň|ŉ' => 'n',
	'Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ' => 'O',
	'ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º' => 'o',
	'Ŕ|Ŗ|Ř' => 'R',
	'ŕ|ŗ|ř' => 'r',
	'Ś|Ŝ|Ş|Š' => 'S',
	'ś|ŝ|ş|š|ſ' => 's',
	'Ţ|Ť|Ŧ' => 'T',
	'ţ|ť|ŧ' => 't',
	'Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ' => 'U',
	'ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ' => 'u',
	'Ý|Ÿ|Ŷ' => 'Y',
	'ý|ÿ|ŷ' => 'y',
	'Ŵ' => 'W',
	'ŵ' => 'w',
	'Ź|Ż|Ž' => 'Z',
	'ź|ż|ž' => 'z',
	'Æ|Ǽ' => 'AE',
	'ß'=> 'ss',
	'Ĳ' => 'IJ',
	'ĳ' => 'ij',
	'Œ' => 'OE',
	'ƒ' => 'f'
    );
    
    foreach($foreign_characters as $old => $new)
    {
        $str = str_replace(explode('|', $old), $new, $str);
    }
    
    if ($separator == 'dash')
    {
            $search	= '_';
            $replace	= '-';
    }
    else
    {
            $search	= '-';
            $replace	= '_';
    }

    $trans = array(
            '&\#\d+?;'			=> '',
            '&\S+?;'			=> '',
            '\s+'			=> $replace,
            '[^a-z0-9\-\._]'		=> '',
            $replace.'+'		=> $replace,
            $replace.'$'		=> $replace,
            '^'.$replace		=> $replace,
            '\.+$'			=> ''
    );

    $str = strip_tags($str);

    foreach ($trans as $key => $val)
    {
            $str = preg_replace("#".$key."#i", $val, $str);
    }

    if ($lowercase === TRUE)
    {
            $str = strtolower($str);
    }
    
    $str = trim(stripslashes($str));
    
    if(strlen($str) == 0 && $is_some_input) $str = '-';
    
    return $str;
}