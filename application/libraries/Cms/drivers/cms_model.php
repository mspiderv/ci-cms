<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Model extends CI_Driver {

    // Členské premenné
    protected $CI;
    protected $uni_model_table = 'uni_model';
    protected $model_table_temp;
    protected $db_object = NULL;
    protected $autoload = array();
    protected $autoload_system = array('langs');
    protected $autoload_user = array();
    protected $table_config = array();
    protected $custom_config = NULL;
    
    // Table prefixes
    public $admin_table_prefix = 'a_';
    public $admin_table = 'admin';
    
    public $eshop_table_prefix = 'e_';
    public $eshop_table = 'eshop';
    
    public $system_table_prefix = 's_';
    public $system_table = 'system';
    
    public $user_table_prefix = 'u_';
    public $user_table = 'user';
    
    // Statické premenné
    protected static $loaded_models = array();

    /* Konštruktor */
    function __construct()
    {
        $this->CI = & get_instance();

        // Load
        $this->CI->load->helper('cms');
        $this->CI->config->load('tables');

        // Initialization
        if (empty($this->table_config)) {
            $this->table_config = cfg('table_config');
        }
    }

    // Verejné metódy
    function autoload() {
        foreach ($this->autoload as $model) {
            $this->load($model);
        }

        foreach ($this->autoload_system as $system_model) {
            $this->load_system($system_model);
        }

        foreach ($this->autoload_user as $user_model) {
            $this->load_user($user_model);
        }
    }

    function load_admin($model, $name = '', $with_prefix = TRUE, $db_object = NULL) {
        $this->load($this->admin_table($model), ($with_prefix ? $this->admin_table_prefix : '') . ((strlen($name) > 0) ? $name : $model), $db_object);
    }

    function load_eshop($model, $name = '', $with_prefix = TRUE, $db_object = NULL) {
        $this->load($this->eshop_table($model), ($with_prefix ? $this->eshop_table_prefix : '') . ((strlen($name) > 0) ? $name : $model), $db_object);
    }

    function load_system($model, $name = '', $with_prefix = TRUE, $db_object = NULL) {
        $this->load($this->system_table($model), ($with_prefix ? $this->system_table_prefix : '') . ((strlen($name) > 0) ? $name : $model), $db_object);
    }

    function load_user($model, $name = '', $with_prefix = TRUE, $db_object = NULL) {
        $this->load($this->user_table($model), ($with_prefix ? $this->user_table_prefix : '') . ((strlen($name) > 0) ? $name : $model), $db_object);
    }
    
    function load_auto($model, $name = '', $with_prefix = TRUE, $db_object = NULL)
    {
        switch(substr($model, 0, 2))
        {
            case 'a_':
                return $this->load_admin($model, $name, $with_prefix, $db_object);
                break;
            
            case 'e_':
                return $this->load_eshop($model, $name, $with_prefix, $db_object);
                break;
            
            case 's_':
                return $this->load_system($model, $name, $with_prefix, $db_object);
                break;
            
            case 'u_':
                return $this->load_user($model, $name, $with_prefix, $db_object);
                break;
            
            default:
                return $this->load($model, $name, $db_object);
                break;
        }
    }

    function load($model, $name = '', $db_object = NULL) {

        // TODO: Spravit ochranu aby sa nedal nacitat model s nazvom ktory konci retzazcom "_lang"
        // Tento retazec (lang_table_suffix) spravit ako premennu konfiguracie a nacitavat ho aj v uni_model, lebo teraz je staticky

        $model = trim($model);
        $name = trim($name);

        if (!in_array($model, self::$loaded_models)) {
            $this->model_table_temp = $model;

            if ($name instanceof CI_DB_driver) {
                $db_object = $name;
                $name = '';
            } else {
                if (!@$name = (string) $name) {
                    $name = '';
                }
            }

            if ($db_object !== NULL) {
                if ($db_object instanceof CI_DB_driver) {
                    $this->db_object = $db_object;
                    $this->db_object->ar_from = array();
                    $this->db_object = $this->db_object->get($model);
                } else {
                    show_error("Zadaný objekt databázy nie je validný. Objekt musí byť typu <strong>CI_DB_driver</strong>. Skontrolujte, či ste použili metódu <strong>get()</strong> a či ste náhodou nepoužili metódu <strong>result()</strong> alebo <strong>result_array()</strong>.");
                }
            }

            $real_model_name = $model . '_model';
            $model_name = ((strlen($name) > 0) ? $name : $model) . '_model';
            $this->CI->load->model($this->uni_model_table, $real_model_name);
            $this->CI->$model_name =& $this->CI->$real_model_name;
            
            self::$loaded_models[$model] = $model_name;
        }
        else {
            $model_name = ((strlen($name) > 0) ? $name : $model) . '_model';

            $old_model_name = self::$loaded_models[$model];

            $this->CI->$model_name =& $this->CI->$old_model_name;
        }
    }

    function get_config()
    {
        if(isset($this->custom_config))
        {
            return $this->custom_config;
            unset($this->custom_config);
        }
        
        if(isset($this->table_config[$this->model_table_temp]))
        {
            return $this->table_config[$this->model_table_temp];
        }
        
        $key = preg_replace('/[0-9]+/', '{int}', $this->model_table_temp);
        if(isset($this->table_config[$key]))
        {
            return $this->table_config[$key];
        }
        
        return NULL;
    }
    
    function use_min_id($table = '')
    {
        return in_array($table, cfg('tables_min_id'));
    }

    function set_custom_config($config) {
        $this->custom_config = $config;
    }

    function get_table() {
        return $this->model_table_temp;
    }

    function get_db_object() {
        $db_object = $this->db_object;
        $this->db_object = NULL;
        return ($db_object instanceof CI_DB_result) ? $db_object : NULL;
    }

    function admin_table($table) {
        return $this->admin_table . '_' . $table;
    }

    function eshop_table($table) {
        return $this->eshop_table . '_' . $table;
    }

    function system_table($table) {
        return $this->system_table . '_' . $table;
    }

    function user_table($table) {
        return $this->user_table . '_' . $table;
    }

    static function get_loaded_models() {
        return self::$loaded_models;
    }

}