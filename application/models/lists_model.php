<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lists_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->model->load_system('lists');
    }
    
    function list_exists($list_id = '')
    {
        return $this->s_lists_model->item_exists($list_id);
    }
    
    function get_list_data($list_id = '')
    {
        if(!$this->list_exists($list_id)) return array();
        
        // Load list model
        $list_type_id = $this->s_lists_model->get_item_data($list_id, 'list_type_id');
        $model = 'list_type_data_' . $list_type_id;
        $this->cms->model->load_user($model);
        $model = 'u_' . $model . '_model';
        
        // Return data
        $this->$model->where('list_id', '=', $list_id);
        return $this->$model->get_data();
    }
    
}