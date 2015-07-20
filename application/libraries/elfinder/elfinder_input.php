<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Elfinder_input {
    
    public function start($options)
    {
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
        
        $connector = new elFinderConnector(new elFinder($options));
        $connector->run();
    }
    
}