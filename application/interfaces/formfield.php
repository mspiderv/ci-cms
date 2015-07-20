<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

interface IFormField {
    
    public function get_field_tpl();
    
    public function is_multilingual();
    
    public function get_title();
    
    public function get_field();
    
    public function get_info();
    
    public function get_error();
    
}