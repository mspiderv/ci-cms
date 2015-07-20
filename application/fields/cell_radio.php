<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('radio');

class Cell_RadioField extends RadioField {
    
    function get_field_tpl()
    {
        return 'table_field_wrap';
    }
    
}