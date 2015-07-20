<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Themes extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('themes');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        
        foreach($this->s_themes_model->get_data() as $theme)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $theme->id, $theme->name));
            $this->admin->form->cell($theme->folder);
            $this->admin->form->cell((strlen($theme->favicon) > 0) ? $this->admin->form->cell_image($theme->favicon) : __('no_favicon'));
            $this->admin->form->cell($this->admin->form->cell_radio('~/set_active_theme/' . $theme->id, __('button_7'), (db_config('active_theme_id') == $theme->id), __('confirm_2')));
            $this->admin->form->cell((db_config('active_theme_id') == $theme->id) ? '' : admin_anchor('~/delete/' . $theme->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $theme->id), 'edit');
            $contextmenu[] = array(__('button_7'), admin_url('~/set_active_theme/' . $theme->id), 'check', __('confirm_2'));
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $theme->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($theme->id, 0, $this->cms->model->system_table('themes'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function set_active_theme($theme_id = '')
    {
        if($this->s_themes_model->item_exists($theme_id))
        {
            db_config('active_theme_id', $theme_id);
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['folder'] = $this->input->post('folder');
            $data['favicon'] = $this->input->post('favicon');
            
            $this->s_themes_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'folder', __('field_2'));
        $this->admin->form->add_field('imagepicker', 'favicon', __('field_3'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($theme_id = '')
    {
        if(!$this->s_themes_model->item_exists($theme_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['folder'] = $this->input->post('folder');
            $data['favicon'] = $this->input->post('favicon');
            
            $this->s_themes_model->set_item_data($theme_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_themes_model->$theme_id->name);
        $this->admin->form->add_field('input', 'folder', __('field_2'), $this->s_themes_model->$theme_id->folder);
        $this->admin->form->add_field('imagepicker', 'favicon', __('field_3'), $this->s_themes_model->$theme_id->favicon);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($theme_id = '')
    {
        if($this->s_themes_model->item_exists($theme_id))
        {
            if(db_config('active_theme_id') == $theme_id)
            {
                $this->admin->form->error(__('error_4'), TRUE);
            }
            else
            {
                $this->s_themes_model->delete_item($theme_id);
                $this->admin->form->message(__('message_3'), TRUE);
            }
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('folder', __('field_2'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('favicon', __('field_3'), 'trim');
    }
    
}