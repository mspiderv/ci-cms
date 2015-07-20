<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panels_in_positions extends CI_Controller {
    
    // TODO: widgetom (aj itemom) spravit contextmenu ???
    // Napriklad by sa tak dali upravovat nazvy kategorii priamo z categorizing
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('panels');
        $this->cms->model->load_system('positions');
        $this->cms->model->load_system('panels_in_positions');
    }
    
    function index()
    {
        $this->admin->form->button_helper(__('helper_1'));
        
        // Create panels
        foreach($this->s_panels_model->get_data_in_col('name') as $panel_id => $panel_name)
        {
            $this->admin->form->categorizing_add_item($panel_id, $panel_name);
        }
        
        // Create positions
        foreach($this->s_positions_model->get_data_in_col('name') as $position_id => $position_name)
        {
            // TODO: do nazvu by asi bolo nejakym sposobom vhodne zakomponovat strukturu (nie len "jablka" ale "potraviny -> ovocie -> jablka")
            $this->admin->form->categorizing_add_widget($position_id, $position_name);
        }
        
        // Add panels to positions
        foreach($this->s_panels_in_positions_model->get_data() as $panel_in_position)
        {
            $this->admin->form->categorizing_add_item_to_widget($panel_in_position->position_id, $panel_in_position->panel_id);
        }
        
        $categorizing_options = array(
            'widget_sort_method' => 'update_positions',
            'item_sort_method' => 'update_panels',
            'unique' => FALSE,
            'unique_in_widget' => FALSE,
            'delete_item' => __('label_1')
        );
        
        $this->admin->form->categorizing($categorizing_options);
        
        $this->admin->form->generate();
    }
    
    function update_positions()
    {
        if(!$this->input->is_ajax_request()) show_404();
        
        $data = array();
        
        $table = $this->s_positions_model->get_table();
        $items = $this->s_positions_model->get_ids();
        $sort = json_decode($this->input->post('sort'));
        
        $result = $this->cms->save_sort($table, $items, $sort);
        
        if($result === TRUE)
        {
            $data['result'] = TRUE;
            $data['error'] = '';
        }
        elseif($result == 'error_1')
        {
            $data['result'] = FALSE;
            $data['error'] = __('error_4');
        }
        elseif($result == 'error_2')
        {
            $data['result'] = FALSE;
                $data['error'] = __('error_3');
        }
        else
        {
            $data['result'] = FALSE;
            $data['error'] = '';
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function update_panels()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        $data['result'] = TRUE;
        $data['error'] = '';
        
        $position_id = $this->input->post('category_id');
        $panel_ids = (array)json_decode($this->input->post('sort'));
        
        if($this->s_positions_model->item_exists($position_id))
        {
            $this->s_panels_in_positions_model->where('position_id', '=', $position_id);
            $this->s_panels_in_positions_model->delete();

            foreach($panel_ids as $panel_id)
            {
                if($this->s_panels_model->item_exists($panel_id))
                {
                    $panel_in_position_data = array();

                    $panel_in_position_data['position_id'] = $position_id;
                    $panel_in_position_data['panel_id'] = $panel_id;

                    $this->s_panels_in_positions_model->add_item($panel_in_position_data);
                }
                else
                {
                    $data['result'] = FALSE;
                    $data['error'] = sprintf(__('error_1'), $panel_id, $position_id);
                }
            }
        }
        else
        {
            $data['result'] = FALSE;
            $data['error'] = sprintf(__('error_2'), $position_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
}