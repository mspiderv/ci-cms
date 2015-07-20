<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function admin_user_name()
{
    $CI =& get_instance();
    $CI->load->driver('admin');
    
    return $CI->admin->auth->get_user_name($CI->admin->auth->get_user_id());
}

function admin_user_id()
{
    $CI =& get_instance();
    $CI->load->driver('admin');
    
    return $CI->admin->auth->get_user_id();
}