<?php

class Image_resize {

    var $image;
    var $image_type;

    function load($filename)
    {
        // Get image type
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];

        // Create image
        switch($this->image_type)
        {
            // JPG / JPEG
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filename);
                break;

            // GIF
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;

            // PNG
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;
        }
    }

    function save($filename)
    {
        switch($this->image_type)
        {
            // JPG / JPEG
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $filename, 100);
                break;

            // GIF
            case IMAGETYPE_GIF:
                imagegif($this->image, $filename);
                break;

            // PNG
            case IMAGETYPE_PNG:
                imagepng($this->image, $filename);
                break;
        }
    }

    function output() {
        switch($this->image_type)
        {
            // JPG / JPEG
            case IMAGETYPE_JPEG:
                imagejpeg($this->image);
                break;

            // GIF
            case IMAGETYPE_GIF:
                imagegif($this->image);
                break;

            // PNG
            case IMAGETYPE_PNG:
                imagepng($this->image);
                break;
        }
    }

    function getWidth()
    {
        return imagesx($this->image);
    }

    function getHeight()
    {
        return imagesy($this->image);
    }

    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);

        switch($this->image_type)
        {
            // GIF
            case IMAGETYPE_GIF:
                $trnprt_indx = imagecolortransparent($this->image);
                // If we have a specific transparent color
                if ($trnprt_indx >= 0) {
                    // Get the original image's transparent color's RGB values
                    $trnprt_color    = imagecolorsforindex($this->image, $trnprt_indx);
                    // Allocate the same color in the new image resource
                    $trnprt_indx    = imagecolorallocate($new_image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);              
                    // Completely fill the background of the new image with allocated color.
                    imagefill($new_image, 0, 0, $trnprt_indx);              
                    // Set the background color for new image to transparent
                    imagecolortransparent($new_image, $trnprt_indx);
                }
                break;

            // PNG
            case IMAGETYPE_PNG:
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                break;
        }
        
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

}

function path2thumb($text) {
    return str_replace('/', '-', $text);
}

// Nastavenie MIME typu na obrázok JPG
header('Content-Type: image/jpeg');

// Definovanie konštánt
define('PATH', '');
define('IMAGES', '');
define('THUMBS', "assets/thumbs/");
define('PREFIX', (@$_GET['minimum'] == 1) ? 'min' : 'max');
define('ROZMERY', PREFIX . '-' . $_GET['width'] . '-' . $_GET['height'] . '-');

// Získanie názvu obrázka
$img_filename = urldecode(@$_GET['img']);

// Overenie existencie malého náhľadu
if(file_exists(PATH . THUMBS . ROZMERY . path2thumb($img_filename)))
{
    $image = new Image_resize();
    $image->load(PATH . THUMBS . ROZMERY . path2thumb($img_filename));
    $image->output();
}

else
{
    // Overenie existencie pôvodného obrázka
    
    if(file_exists(PATH . IMAGES . $img_filename))
    {
        // Vytvorenie náhľadu
        $image = new Image_resize();
        $image->load(PATH . IMAGES . $img_filename);

        // Výpočet veľkosti obrázka
        $width = $image->getWidth();
        $height = $image->getHeight();

        $factor = $height / $width;

        if(@$_GET['minimum'] == 1)
        {
            while($width > $_GET['width'] && $height > $_GET['height'])
            {
                $width--;
                $height -= $factor;
            }
        }
        
        else
        {
            while($width > $_GET['width'] || $height > $_GET['height'])
            {
                $width--;
                $height -= $factor;
            }
        }

        $height = ceil($height);

        $image->resize($width, $height);
        $image->save(PATH . THUMBS . ROZMERY . path2thumb($img_filename));

        // Zobrazenie náhľadu
        echo file_get_contents(PATH . THUMBS . ROZMERY . path2thumb($img_filename));

        if(@$_GET['nocache'] == 1)
        {
            @unlink(PATH . THUMBS . ROZMERY . path2thumb($img_filename));
        }
    }
}
