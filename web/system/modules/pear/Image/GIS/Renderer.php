<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Image :: GIS :: Renderer Base Class                            |
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
// $Id: Renderer.php,v 1.14 2004/07/25 13:56:29 sebastian Exp $
//

require_once 'Image/Color.php';

/**
 * Renderer Base Class.
 *
 * @author      Jan Kneschke <jan@kneschke.de>
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2002-2004 Jan Kneschke <jan@kneschke.de> and Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.php.net/license/3_0.txt The PHP License, Version 3.0
 * @category    Image
 * @package     Image_GIS
 * @abstract
 */
class Image_GIS_Renderer {
    /**
    * Set to TRUE to enable debugging.
    *
    * @var boolean $debug
    */
    var $debug;

    /**
    * @var array $min
    */
    var $min = false;

    /**
    * @var array $max
    */
    var $max = false;

    /**
    * Width of the image.
    *
    * @var integer $width
    */
    var $width;

    /**
    * Height of the image.
    *
    * @var integer $height
    */
    var $height;

    /**
    * Constructor.
    *
    * @param  mixed   $width
    * @param  integer $height
    * @param  boolean $debug
    * @access public
    */
    function Image_GIS_Renderer($width, $height, $debug) {
        $this->debug  = $debug;

        if ($width < 0 ||
            $width > 2048) {
            $width = 640;
        }

        if ($height < 0 ||
            $height > 2048) {
            $height = 480;
        }

        $this->width  = $width;
        $this->height = $height;
   }

    /**
    * Factory.
    *
    * @param  string  $renderer
    * @param  mixed   $width
    * @param  integer $height
    * @param  boolean $debug
    * @return object
    * @access public
    */
    function &factory($renderer, $width, $height, $debug) {
        if (@include_once('Image/GIS/Renderer/' . $renderer . '.php')) {
            $class  = 'Image_GIS_Renderer_' . $renderer;
            $object = new $class($width, $height, $debug);

            return $object;
        }
    }

    /**
    * Draws a clipped line from ($x1, $y1) to ($x2, $y2)
    * using $color.
    *
    * @param  float $x1
    * @param  float $y1
    * @param  float $x2
    * @param  float $y2
    * @param  mixed $color
    * @access public
    */
    function drawClippedLine($x1, $y1, $x2, $y2, $color) {
        if (($x1 > $this->max['x']  ||
             $x1 < $this->min['x']  ||
             $y1 > $this->max['y']  ||
             $y1 < $this->min['y']) &&
            ($x2 > $this->max['x']  ||
             $x2 < $this->min['x']  ||
             $y2 > $this->max['y']  ||
             $y2 < $this->min['y'])) {
            if ($this->debug) {
                printf('clipped x1: %d %d %d<br />', $x1, $this->min['x'], $this->max['x']);
                printf('clipped y1: %d %d %d<br />', $y1, $this->min['y'], $this->max['y']);
                printf('clipped x2: %d %d %d<br />', $x2, $this->min['x'], $this->max['x']);
                printf('clipped y2: %d %d %d<br />', $y2, $this->min['y'], $this->max['y']);
            }
        } else {
            if (!is_array($color)) {
                $color = Image_Color::namedColor2RGB($color);
            }

            $x1 = $this->polar2image($x1, 'x');
            $y1 = $this->polar2image($y1, 'y');
            $x2 = $this->polar2image($x2, 'x');
            $y2 = $this->polar2image($y2, 'y');

            if ($this->debug) {
                printf('Drawing line (%s, %s) -> (%s, %s)<br />', $x1, $y1, $x2, $y2);
            }

            $this->drawLine($x1, $y1, $x2, $y2, $color[0], $color[1], $color[2]);
        }
    }

    /**
    * Returns the range of the data to be rendered.
    *
    * @return array
    * @access public
    * @since  Image_GIS 1.0.1
    */
    function getRange() {
        return array(
          $this->min['x'],
          $this->max['x'],
          $this->min['y'],
          $this->max['y']
        );
    }

    /**
    * Converts a polar coordinate to an image coordinate.
    *
    * @param  float  $polarCoordinate
    * @param  string $direction
    * @access public
    */
    function polar2image($polarCoordinate, $direction) {
        switch ($direction) {
            case 'x': {
                return ($polarCoordinate - $this->min[$direction]) *
                       ($this->width / ($this->max[$direction] - $this->min[$direction]));
            }
            break;

            case 'y': {
                return ($polarCoordinate - $this->max[$direction]) *
                       ($this->height / ($this->min[$direction] - $this->max[$direction]));
            }
            break;
        }
    }

    /**
    * Renders the image.
    *
    * @param  array $lineSets
    * @access public
    */
    function render($lineSets) {
        if ($this->min == false || $this->max == false) {
            foreach ($lineSets as $lineSet) {
                if ($this->min == false) {
                    $this->min['x'] = $lineSet->min['x'];
                    $this->min['y'] = $lineSet->min['y'];
                    $this->max['x'] = $lineSet->max['x'];
                    $this->max['y'] = $lineSet->max['y'];
                } else {
                    $this->min['x'] = min($this->min['x'], $lineSet->min['x']);
                    $this->min['y'] = min($this->min['y'], $lineSet->min['y']);
                    $this->max['x'] = max($this->max['x'], $lineSet->max['x']);
                    $this->max['y'] = max($this->max['y'], $lineSet->max['y']);
                }
            }
        }

        foreach ($lineSets as $lineSet) {
            foreach ($lineSet->lines as $line) {
                $this->drawClippedLine($line[0], $line[1], $line[2], $line[3], $lineSet->color);
            }
        }
    }

    /**
    * Sets the range of the data to be rendered.
    *
    * @param  float $x1
    * @param  float $x2
    * @param  float $y1
    * @param  float $y2
    * @access public
    */
    function setRange($x1, $x2, $y1, $y2) {
        $this->min = array('x' => $x1, 'y' => $y1);
        $this->max = array('x' => $x2, 'y' => $y2);
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
    * @abstract
    */
    function drawLine($x1, $y1, $x2, $y2, $r, $g, $b) { /* abstract */ }

    /**
    * Saves the rendered image to a given file.
    *
    * @param  string  $filename
    * @return boolean
    * @access public
    * @abstract
    */
    function saveImage($filename) { /* abstract */ }

    /**
    * Shows the rendered image.
    *
    * @access public
    * @abstract
    */
    function showImage() { /* abstract */ }
}
?>
