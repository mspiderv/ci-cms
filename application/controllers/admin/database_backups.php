<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Database_backups extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        
        $this->load->library('database_deposit');
    }
    
    function index()
    {
        $backups = $this->database_deposit->get_backups();
        sort($backups, SORT_STRING);
        $backups = array_reverse($backups);
        
        $this->admin->form->button_admin_link('~/backup', __('button_1'), 'arrowthickstop-1-s');
        if(count($backups) > 0)
        {
            $this->admin->form->button_admin_link('~/restore/' . @$backups[0], __('button_2'), 'arrowreturnthick-1-n', __('confirm_1'));
            $this->admin->form->button_admin_link('~/delete_all', __('button_3'), 'trash', __('confirm_2'));
        }
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'), 165);
        $this->admin->form->col(__('col_3'), 180);
        
        $id = 0;
        
        $this->admin->form->listing_sortable = FALSE;
        
        foreach($backups as $backup)
        {
            $id++;
            
            $download_cell  = admin_anchor('~/download/txt/' . $backup, 'TXT');
            $download_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $download_cell .=  admin_anchor('~/download/sql/' . $backup, 'SQL');
            $download_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $download_cell .=  admin_anchor('~/download/zip/' . $backup, 'ZIP');
            $download_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $download_cell .=  admin_anchor('~/download/gzip/' . $backup, 'GZIP');
            
            $options_cell  = admin_anchor('~/delete/' . $backup, __('button_4'), __('confirm_4'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/edit/' . $backup, __('button_10'));
            
            $this->admin->form->cell(admin_anchor('~/restore/' . $backup, $backup, __('confirm_3')));
            $this->admin->form->cell($download_cell);
            $this->admin->form->cell($options_cell);
            
            $contextmenu = array();
            
            $contextmenu[] = array(__('button_12'), admin_url('~/restore/' . $backup), 'restore', __('confirm_3'));
            $contextmenu[] = array(__('button_10'), admin_url('~/edit/' . $backup), 'edit');
            $contextmenu[] = array(__('button_5'), admin_url('~/download/txt/' . $backup), 'download');
            $contextmenu[] = array(__('button_6'), admin_url('~/download/sql/' . $backup), 'download');
            $contextmenu[] = array(__('button_7'), admin_url('~/download/zip/' . $backup), 'download');
            $contextmenu[] = array(__('button_8'), admin_url('~/download/gzip/' . $backup), 'download');
            $contextmenu[] = array(__('button_4'), admin_url('~/delete/' . $backup), 'delete', __('confirm_4'));
            
            $this->admin->form->row($id, NULL, NULL, NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    function download($type = '', $backup = '')
    {
        if($this->database_deposit->backup_exists($backup))
        {
            $backup_data = $this->database_deposit->read($backup);
            
            if($backup_data === FALSE)
            {
                $this->admin->form->error(__('error_1'), TRUE);
                admin_redirect();
            }
            
            switch($type)
            {
                case 'txt':
                case 'sql':
                    $this->load->helper('download');
                    force_download($backup . '.' . $type, $backup_data);
                    break;
                
                case 'zip':
                case 'gzip':
                    $this->load->helper('download');
                    $this->load->library('zip');
                    $this->zip->add_data($backup . '.sql', $backup_data);
                    force_download($backup . '.' . $type, $this->zip->get_zip());
                    break;
            }
        }
    }
    
    function backup()
    {
        $this->database_deposit->backup();
        $this->admin->form->message(__('message_1'), TRUE);
        admin_redirect();
    }
    
    function restore($file = '')
    {
        if($this->database_deposit->backup_exists($file))
        {
            $this->database_deposit->restore($file);
            $this->admin->form->message(__('message_2'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function edit($file = '')
    {
        if($this->database_deposit->backup_exists($file))
        {
            $this->admin->form->set_rules('backup_name', __('field_1'), 'trim|alpha_dash|no_database_backup_file[' . $this->input->post('backup_name') . ']|required');
            
            if($this->admin->form->validate())
            {
                $this->database_deposit->delete_backup($file);
                $this->database_deposit->write($this->input->post('backup_name'), $this->input->post('backup_content'));
                $this->admin->form->message(__('message_5'), TRUE);
                if(url_param() == 'accept') admin_redirect('~/edit/' . $this->input->post('backup_name'));
                else admin_redirect();
            }
            
            $content = $this->database_deposit->read($file);
            $this->admin->form->add_field('input', 'backup_name', __('field_1'), $file);
            $this->admin->form->add_field('codemirror', 'backup_content', 'mysql', __('field_2'), $content);
            
            $this->admin->form->button_submit(__('button_9'));
            $this->admin->form->button_submit(__('button_10'), 'accept', 'check');
            $this->admin->form->button_index();
            $this->admin->form->generate_buttons();

            $this->admin->form->generate();
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
            admin_redirect();
        }
    }
    
    function delete($backup)
    {
        if($this->database_deposit->backup_exists($backup))
        {
            $this->database_deposit->delete_backup($backup);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function delete_all()
    {
        if($this->database_deposit->delete_all_backups())
        {
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
}