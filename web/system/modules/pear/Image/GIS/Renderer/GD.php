<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Image :: GIS :: GD Renderer                                    |
// +------------------------------------------------------------------------+
// | Copyright (c) 2002-2004 Jan Kneschke <jan@kneschke.de> and             |
// |                         Sebastian Bergmann <sb@sebastian-bergmann.de>. |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
// $Id: GD.php,v 1.7 2004/04/17 10:21:26 sebastian Exp $
//

require_once 'Image/GIS/Renderer.php';

/**
 * GD Renderer.
 *
 * @author      Jan Kneschke <jan@kneschke.de>
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2002-2004 Jan Kneschke <jan@kneschke.de> and Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.php.net/license/3_0.txt The PHP License, Version 3.0
 * @category    Image
 * @package     Image_GIS
 */
class Image_GIS_Renderer_GD extends Image_GIS_Renderer {
    /**
    * GD Image Palette.
    *
    * @var array $palette
    */
    var $palette = array();

    /**
    * GD Image Ressource.
    *
    * @var ressource $img
    */
    var $img;

    /**
    * Constructor.
    *
    * @param  mixed   $width
    * @param  integer $sizyY
    * @param  boolean $debug
    * @access public
    */
    function Image_GIS_Renderer_GD($width, $height, $debug) {
        if (is_file($width)) {
            $this->img = imagecreatefrompng($width);
            $width     = imagesx($this->img);
            $height    = imagesy($this->img);

            $this->Image_GIS_Renderer($width, $height, $debug);
        } else {
            $this->Image_GIS_Renderer($width, $height, $debug);

            $this->img = imagecreate($this->width, $this->height);
            imagecolorallocate($this->img, 255, 255, 255);
        }
    }

    /**
    * Draws a line from ($x1, $y1) to ($x2, $y2)
    * using the color rgb($r, $g, $b).
    *
    * @param  float   $x1
    * @param  float   $y1
    * @param  float   $x2
    * @param  float   $y2
    * @param  float   $r
    * @param  float   $g
    * @param  float   $b
    * @access public
    */
    function drawLine($x1, $y1, $x2, $y2, $r, $g, $b) {
        if (!isset($this->palette[$r][$g][$b])) {
            $this->palette[$r][$g][$b] = imagecolorallocate($this->img, $r, $g, $b);
        }

        imageline(
          $this->img,
          $x1,
          $y1,
          $x2,
          $y2,
          $this->palette[$r][$g][$b]
        );
    }

    /**
    * Saves the rendered image to a given file.
    *
    * @param  string  $filename
    * @return boolean
    * @access public
    */
    function saveImage($filename) {
        return imagepng($this->img, $filename);
    }

    /**
    * Shows the rendered image.
    *
    * @access public
    */
    function showImage() {
        header('Content-Type: image/png');
        imagepng($this->img);
    }
}
?>
