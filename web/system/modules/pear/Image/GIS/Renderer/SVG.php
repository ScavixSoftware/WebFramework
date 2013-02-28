<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Image :: GIS :: SVG Renderer                                   |
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
// $Id: SVG.php,v 1.8 2004/04/17 10:21:26 sebastian Exp $
//

require_once 'Image/GIS/Renderer.php';
require_once 'XML/SVG.php';

/**
 * SVG Renderer.
 *
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2002-2004 Jan Kneschke <jan@kneschke.de> and Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.php.net/license/3_0.txt The PHP License, Version 3.0
 * @category    Image
 * @package     Image_GIS
 */
class Image_GIS_Renderer_SVG extends Image_GIS_Renderer {
    /**
    * SVG Document.
    *
    * @var XML_SVG $svg
    */
    var $svg;

    /**
    * SVG Groups.
    *
    * @var XML_SVG_Group[]
    */
    var $svgGroups = array();

    /**
    * Constructor.
    *
    * @param  mixed   $width
    * @param  integer $sizyY
    * @param  boolean $debug
    * @access public
    */
    function Image_GIS_Renderer_SVG($width, $height, $debug) {
        $this->Image_GIS_Renderer($width, $height, $debug);

        $this->svg = new XML_SVG_Document(
          array(
            'width'  => $width,
            'height' => $height
          )
        );
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
        $group = md5($r . $g . $b);

        if (!isset($this->svgGroups[$group])) {
            $this->svgGroups[$group] = new XML_SVG_Group(
              array(
                'style' => sprintf(
                  'stroke:rgb(%s, %s, %s)',

                  $r,
                  $g,
                  $b
                )
              )
            );

            $this->svgGroups[$group]->addParent($this->svg);
        }

        $line = new XML_SVG_Line(
          array(
            'x1'    => $x1,
            'y1'    => $y1,
            'x2'    => $x2,
            'y2'    => $y2,
          )
        );

        $this->svgGroups[$group]->addChild($line);
    }

    /**
    * Saves the rendered image to a given file.
    *
    * @param  string  $filename
    * @return boolean
    * @access public
    */
    function saveImage($filename) {
        if ($fp = @fopen($filename, 'w')) {
            @fputs($fp, $this->svg->bufferObject());
            @fclose($fp);

            return true;
        }

        return false;
    }

    /**
    * Shows the rendered image.
    *
    * @access public
    */
    function showImage() {
        $this->svg->printElement();
    }
}
?>
