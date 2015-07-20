<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Export extends CI_Driver {
    
    protected $CI;
    protected $content;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->content = array();
    }
    
    function clear()
    {
        $this->content = array();
    }
    
    function panel_type($panel_type_id = '')
    {
        $this->CI->cms->model->load_system('panel_types');
        
        if($this->CI->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->CI->load->dbutil();
            
            $this->CI->cms->model->load_user('panel_type_data_' . $panel_type_id, 'panel_type_data_X');
            $this->CI->cms->model->load_system('panels');
            
            $prefs = array(
                'tables'      => array($this->CI->db->dbprefix . $this->CI->cms->model->user_table('panel_type_data_' . $panel_type_id)),
                'format'      => 'txt',
                'add_drop'    => FALSE,
                'add_insert'  => FALSE
            );
            
            $backup = $this->CI->dbutil->backup($prefs);
            $backup = str_replace(PHP_EOL, ' ', $backup);
            
            $backup = str_replace('CONSTRAINT `' . $this->CI->db->dbprefix . $this->CI->cms->model->user_table('panel_type_data_' . $panel_type_id), 'CONSTRAINT `{[$#1#$]}', $backup);
            $backup = str_replace('FOREIGN KEY (`' . $this->CI->u_panel_type_data_X_model->get_col('id') . '`) REFERENCES `' . $this->CI->db->dbprefix . $this->CI->cms->model->system_table('panels') . '` (`' . $this->CI->s_panels_model->get_col('id') . '`)', 'FOREIGN KEY (`{[$#2#$]}`) REFERENCES `{[$#3#$]}` (`{[$#4#$]}`)', $backup);
            
            $content = array();
            $content['variables'] = array();
            
            $content['backup'] = $backup;
            $content['name'] = $this->CI->s_panel_types_model->$panel_type_id->name;
            $content['class'] = $this->CI->s_panel_types_model->$panel_type_id->class;
            $content['method'] = $this->CI->s_panel_types_model->$panel_type_id->method;
            $content['tpl'] = $this->CI->s_panel_types_model->$panel_type_id->tpl;
            
            foreach($this->CI->cms->panels->get_panel_type_variables($panel_type_id) as $panel_type_variable)
            {
                $content['variables'][] = array(
                    'name' => $panel_type_variable->name,
                    'title' => $panel_type_variable->title,
                    'info' => (strlen($panel_type_variable->info) > 0) ? $panel_type_variable->info : NULL,
                    'add' => intval($panel_type_variable->add),
                    'edit' => intval($panel_type_variable->edit),
                    'field_type' => $panel_type_variable->name
                );
            }
            
            $this->content['panel_types'][$panel_type_id] = $content;
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get($serialized = FALSE)
    {
        return ($serialized) ? serialize($this->content) : $this->content;
    }
    
    function download()
    {
        $this->CI->load->helper('download');
        force_download('export.txt', $this->get(TRUE));
    }
    
}