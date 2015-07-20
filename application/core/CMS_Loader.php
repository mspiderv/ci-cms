<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Loader extends CI_Loader {

    public function load_interface($interface)
    {
        include_once './' . APPPATH . 'interfaces/' . strtolower($interface) . EXT;
    }
    
    public function load_class($class)
    {
        include_once './' . APPPATH . 'classes/' . strtolower($class) . EXT;
    }
    
}