<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate($type = '')
{    
    $CI =& get_instance();
    $CI->load->driver('parse');
    $method = '_generate_' . $type;
    if(!method_exists($CI->parse, $method)) show_error('Funkcia <strong>generate</strong> prijala neočakávaný parameter <strong>' . $type . '</strong>.');
    return call_user_func_array(array($CI->parse, $method), array_slice(func_get_args(), 1));
}

function menu($links = array(), $settings = array(), $level = 0)
{
    if(!is_array($links) || count($links) == 0) return '';
    
    $defaults = array(
        'menu_open' => '<ul>',
        'menu_close' => '</ul>',
        'link' => 'a',
        'link_open' => '<li>',
        'link_close' => '</li>',
        'link_separator' => '',
        'active_class' => 'active'
    );
    
    foreach(array_keys($defaults) as $key)
    {
        if(!array_key_exists($key, $settings)) $settings[$key] = $defaults[$key];
    }
    
    $first_link = TRUE;
    
    $content = '';
    
    $content .= $settings['menu_open'];
    
    foreach($links as $link)
    {
        if($first_link)
        {
            $first_link = FALSE;
        }
        else
        {
            $content .= $settings['link_separator'];
        }
        
        $json_data = json_decode($link['link']->href);
        $attributes = (array)@$json_data->attrs;
        $attributes['href'] = href($link['link']->href);
        
        if(active_href($link['link']->href)) @$attributes['class'] .= ' ' . $settings['active_class'];
        
        $content .= $settings['link_open'];
        $content .= '<' . $settings['link'] . _attributes_to_string($attributes) . '>';
        $content .= $link['link']->_text;
        $content .= '</' . $settings['link'] . '>';
        
        if(is_array(@$link['subs'])) $content .= menu($link['subs'], $settings, $level + 1);
        
        $content .= $settings['link_close'];
    }
    
    $content .= $settings['menu_close'];
    
    return $content;
}

function get_parse_type()
{
    $CI =& get_instance();
    $CI->load->driver('parse');
    return $CI->parse->url->get_type();
}

function is_on_homepage()
{
    return (get_parse_type() == 'homepage');
}

function is_on_page()
{
    return (get_parse_type() == 'page');
}

function is_on_product()
{
    return (get_parse_type() == 'product');
}

function is_on_category()
{
    return (get_parse_type() == 'category');
}

function is_on_service()
{
    return (get_parse_type() == 'service');
}

function is_on_404()
{
    return (get_parse_type() == '404');
}

function a($href = '', $attrs = array())
{
    $href_data = get_href($href);
    
    $attrs['href'] = href($href);
    $attrs = array_merge($attrs, @(array)$href_data['attrs']);
    
    return '<a' . _parse_attributes($attrs) . '>';
}