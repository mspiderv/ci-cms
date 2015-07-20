<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_product_galleries extends CI_Driver {

    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('product_galleries');
        $this->CI->cms->model->load_eshop('product_gallery_images');
    }
    
    // Primárny obrázok
    
    function is_image_in_gallery($image_id = '', $gallery_id = '')
    {
        if(!$this->CI->e_product_gallery_images_model->item_exists($image_id)) return FALSE;
        return ($this->CI->e_product_gallery_images_model->$image_id->product_gallery_id == $gallery_id);
    }
    
    function get_gallery_primary_image_id($gallery_id = '')
    {
        if(!$this->CI->e_product_galleries_model->item_exists($gallery_id)) return FALSE;
        
        $primary_image_id = $this->CI->e_product_galleries_model->$gallery_id->primary_image_id;
        if(intval($primary_image_id) > 0) return $primary_image_id;

        $this->CI->e_product_gallery_images_model->where('product_gallery_id', '=', $gallery_id);
        return $this->CI->e_product_gallery_images_model->get_first_id();
    }
    
    function set_first_image_as_primary($gallery_id = '')
    {
        if(!$this->CI->e_product_galleries_model->item_exists($gallery_id)) return FALSE;
        
        $this->CI->e_product_gallery_images_model->where('product_gallery_id', '=', $gallery_id);
        $first_image_id = $this->CI->e_product_gallery_images_model->get_first_id();
        if(intval($first_image_id) == 0) $first_image_id = NULL;
        $this->CI->e_product_galleries_model->set_item_data($gallery_id, array('primary_image_id' => $first_image_id));
        return TRUE;
    }
    
    function image_is_primary($image_id = '', $gallery_id = NULL)
    {
        if(!$this->CI->e_product_gallery_images_model->item_exists($image_id)) return FALSE;
        if(intval($gallery_id) > 0 && !$this->CI->e_product_galleries_model->item_exists($gallery_id))return FALSE;
        else $gallery_id = $this->CI->e_product_gallery_images_model->$image_id->product_gallery_id;
        return ($this->CI->e_product_galleries_model->$gallery_id->primary_image_id == $image_id);
    }
    
    function set_primary_image($gallery_id = '', $image_id = '')
    {
        if(!$this->CI->e_product_galleries_model->item_exists($gallery_id)) return FALSE;
        if(!$this->CI->e_product_gallery_images_model->item_exists($image_id)) return FALSE;
        if(!$this->is_image_in_gallery($image_id, $gallery_id)) return FALSE;
        
        return $this->CI->e_product_galleries_model->set_item_data($gallery_id, array('primary_image_id' => $image_id));
    }
    
    // Pomocné metódy
    
    function get_image_filename($image_id = '')
    {
        if(!$this->CI->e_product_gallery_images_model->item_exists($image_id)) return '';
        
        return get_filename_from_path(urldecode($this->CI->e_product_gallery_images_model->get_item_data($image_id, 'url')));
    }
    
}