<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Positions extends CI_Driver {
    
    protected $CI;
    protected $codes = NULL;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('positions');
        $this->CI->cms->model->load_system('panels_in_positions');
    }
    
    function position_exists($position_id = '')
    {
        return $this->CI->s_positions_model->item_exists($position_id);
    }
    
    function is_public($position_id = '')
    {
        if(!$this->position_exists($position_id)) return FALSE;
        
        return (bool)$this->CI->s_positions_model->$position_id->public;
    }
    
    function get_panel_ids($position_id = '')
    {
        if(!$this->position_exists($position_id)) return array();
        
        $this->CI->s_panels_in_positions_model->where('position_id', '=', $position_id);
        return $this->CI->s_panels_in_positions_model->get_data_in_col('panel_id');
    }
    
    function get_position_id_by_code($position_code = '')
    {
        if($this->codes == NULL) $this->codes = array_flip($this->CI->s_positions_model->get_data_in_col('code'));
        
        return (isset($this->codes[$position_code])) ? $this->codes[$position_code] : FALSE;
    }
    
}