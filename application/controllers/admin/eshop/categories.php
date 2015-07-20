<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends CI_Controller {
    
    // TODO: Hromadne upravy (vymazat 3 kategorie naraz a pod.)
    // TODO: spravit listing_indicator ktory ukazuje % alebo nejake cislo napr 4 /9 - 55% a pod. (napr na sitemap_priority a pod.)
    // TODO: spravit "button_reset" (v edite resetuje formular na povodne hodnoty)
    // TODO: spravit multilang fields (moznost prepinat langy a tym mat moznost vyplnit hodnotu fieldu vo vsetkych jazykoch)
    // TODO: langom dat vlajocky (asi to bude lepisie ked to bude zautomatizovane aby sa user s tym nemusel namahat)
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('categories');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        
        foreach($this->eshop->categories->get_categories_structure() as $category)
        {
            $category_id = @$category['id'];
            $category_level = @$category['level'];
            
            $options_cell = '';
            $options_cell .= '<a href="' . href_category($category_id) . '">' . __('button_11') . '</a>';
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/add/' . $category_id, __('button_10'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $category_id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $category_id, $this->e_categories_model->$category_id->_name));
            $this->admin->form->cell($this->e_categories_model->$category_id->_alias);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($this->e_categories_model->$category_id->index) ? '~/unindex_category/' : '~/index_category/') . $category_id, (($this->e_categories_model->$category_id->index) ? __('button_8') : __('button_9')), $this->e_categories_model->$category_id->index));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($this->e_categories_model->$category_id->public) ? '~/unpublish_category/' : '~/publish_category/') . $category_id, (($this->e_categories_model->$category_id->public) ? __('button_6') : __('button_7')), $this->e_categories_model->$category_id->public));
            $this->admin->form->cell($this->admin->form->cell_indicator($this->e_categories_model->$category_id->sitemap_priority, 1));
            $this->admin->form->cell($options_cell);
            
            $contextmenu = array();
            
            $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $category_id), 'edit');
            $contextmenu[] = array(__('button_11'), href_category($category_id), 'show');
            $contextmenu[] = array(__('button_10'), admin_url('~/add/' . $category_id), 'add');
            
            if($this->e_categories_model->$category_id->index) $contextmenu[] = array(__('button_8'), admin_url('~/unindex_category/' . $category_id), 'x');
            else $contextmenu[] = array(__('button_9'), admin_url('~/index_category/' . $category_id), 'check');
            
            if($this->e_categories_model->$category_id->public) $contextmenu[] = array(__('button_6'), admin_url('~/unpublish_category/' . $category_id), 'x');
            else $contextmenu[] = array(__('button_7'), admin_url('~/publish_category/' . $category_id), 'check');
            
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $category_id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($category_id, $category_level, $this->cms->model->eshop_table('categories') . '_' . $this->e_categories_model->$category_id->parent_id, TRUE, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unpublish_category($category_id = '')
    {
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_categories_model->set_item_data($category_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_category($category_id = '')
    {
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_categories_model->set_item_data($category_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function unindex_category($category_id = '')
    {
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_categories_model->set_item_data($category_id, array('index' => FALSE));
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function index_category($category_id = '')
    {
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_categories_model->set_item_data($category_id, array('index' => TRUE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function add($parent_category_id = '')
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $parent_id = (int)$this->input->post('parent_id');
            $parent_id = ($this->e_categories_model->item_exists($parent_id)) ? $parent_id : NULL;
            
            $data['_name'] = $this->input->post('_name');
            $data['_alias'] = $this->input->post('_alias');
            $data['parent_id'] = $parent_id;
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_image'] = $this->input->post('_image');
            $data['_description'] = $this->input->post('_description');
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = ($this->input->post('index') == cfg('form', 'true'));
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $data['tpl'] = $this->input->post('tpl');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['_name']);
            
            $this->e_categories_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('input', '_alias', __('field_2'));
        $this->admin->form->add_field('select', 'parent_id', __('field_3'), $this->eshop->categories->get_categories_select_data(), $parent_category_id, TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), TRUE);
        $this->admin->form->add_field('imagepicker', '_image', __('field_5'));
        $this->admin->form->add_field('ckeditor', '_description', __('field_6'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_11'), '', __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_12'), '', __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_13'), '', __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_7'), cfg('changefreq', 'default'));
        $this->admin->form->add_field('checkbox', 'index', __('field_8'), TRUE);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_9'), cfg('sitemap', 'default_priority'), 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('select', 'tpl', __('field_10'), $this->cms->templates->get_templates_select_data('categories', TRUE));
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }

    function edit($category_id = '')
    {
        if(!$this->e_categories_model->item_exists($category_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($category_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $parent_id = (int)$this->input->post('parent_id');
            $parent_id = ($this->e_categories_model->item_exists($parent_id)) ? $parent_id : NULL;
            
            $data['_name'] = $this->input->post('_name');
            $data['_alias'] = $this->input->post('_alias');
            $data['parent_id'] = $parent_id;
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_image'] = $this->input->post('_image');
            $data['_description'] = $this->input->post('_description');
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = $this->input->post('index');
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $data['tpl'] = $this->input->post('tpl');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['_name']);
            
            $this->e_categories_model->set_item_data($category_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_categories_model->$category_id->_name);
        $this->admin->form->add_field('input', '_alias', __('field_2'), $this->e_categories_model->$category_id->_alias);
        $this->admin->form->add_field('select', 'parent_id', __('field_3'), $this->eshop->categories->get_categories_select_data($category_id), $this->e_categories_model->$category_id->parent_id, TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), (bool)$this->e_categories_model->$category_id->public);
        $this->admin->form->add_field('imagepicker', '_image', __('field_5'), $this->e_categories_model->$category_id->_image);
        $this->admin->form->add_field('ckeditor', '_description', __('field_6'), $this->e_categories_model->$category_id->_description);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_11'), $this->e_categories_model->$category_id->_meta_title, __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_12'), $this->e_categories_model->$category_id->_meta_description, __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_13'), $this->e_categories_model->$category_id->_meta_keywords, __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_7'), $this->e_categories_model->$category_id->changefreq);
        $this->admin->form->add_field('checkbox', 'index', __('field_8'), (bool)$this->e_categories_model->$category_id->index);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_9'), $this->e_categories_model->$category_id->sitemap_priority, 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('select', 'tpl', __('field_10'), $this->cms->templates->get_templates_select_data('categories', TRUE), $this->e_categories_model->$category_id->tpl);
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_submit(__('button_5'), 'accept', 'check');
        $this->admin->form->button_link(site_url(href_category($category_id)), __('button_11'), 'home');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($category_id = '')
    {
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_categories_model->delete_item($category_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($category_id = '')
    {
        $parent_category_validation = (intval($category_id) > 0) ? 'eshop_parent_category_id[' . $category_id . ']' : 'item_exists_eshop[categories]';
        
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_alias', __('field_2'), 'trim|url_title|max_length[255]|valid_alias');
        $this->admin->form->set_rules('parent_id', __('field_3'), 'trim|' . $parent_category_validation);
        $this->admin->form->set_rules('public', __('field_4'), 'trim|intval');
        $this->admin->form->set_rules('_image', __('field_5'), 'trim');
        $this->admin->form->set_rules('_description', __('field_6'), 'trim');
        $this->admin->form->set_rules('_meta_title', __('field_11'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_meta_description', __('field_12'), 'trim');
        $this->admin->form->set_rules('_meta_keywords', __('field_13'), 'trim');
        $this->admin->form->set_rules('changefreq', __('field_7'), 'trim|required|changefreq|max_length[255]');
        $this->admin->form->set_rules('index', __('field_8'), 'trim|intval');
        $this->admin->form->set_rules('sitemap_priority', __('field_9'), 'trim|required|sitemap_priority');
        $this->admin->form->set_rules('tpl', __('field_10'), 'trim|max_length[255]|tpl[categories]');
    }
    
}