<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Image :: GIS :: E00 Parser                                     |
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
// $Id: E00.php,v 1.14 2004/07/25 13:56:29 sebastian Exp $
//

require_once 'Image/GIS/LineSet.php';
require_once 'Image/GIS/Parser.php';

/**
 * E00 Parser.
 *
 * @author      Jan Kneschke <jan@kneschke.de>
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2002-2004 Jan Kneschke <jan@kneschke.de> and Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.php.net/license/3_0.txt The PHP License, Version 3.0
 * @category    Image
 * @package     Image_GIS
 */
class Image_GIS_Parser_E00 extends Image_GIS_Parser {
    /**
    * Constructor.
    *
    * @param  boolean $cache
    * @param  boolean $debug
    * @access public
    */
    function Image_GIS_Parser_E00($cache, $debug) {
        $this->Image_GIS_Parser($cache, $debug);
    }

    /**
    * Parses a data file.
    *
    * @param  string  $dataFile
    * @param  mixed   $color
    * @return mixed
    * @access public
    */
    function parseFile($dataFile, $color) {
        $lineSet = new Image_GIS_LineSet($color);

        if ($fp = @fopen($dataFile, 'r')) {
            $numRecords = 0;
            $ln         = 0;

            while(0 || $line = fgets($fp, 1024)) {
                $ln ++;

                if ($numRecords == 0 && 
                    preg_match("#^\s+([0-9]+)\s+([-0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)#", $line, $a)) {
                    $numRecords = $a[7];

                    $pl['x'] = -1;
                    $pl['y'] = -1;
                }

                else if ($numRecords &&
                         preg_match("#^([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})#", $line, $a)) {
                    if ($this->debug) {
                        echo $a[0] . '<br />';
                    }

                    if ($pl['x'] != -1 &&
                        $pl['y'] != -1) {
                        $lineSet->addLine($pl['x'], $pl['y'], $a[1], $a[2]);
                    }

                    $numRecords--;

                    $lineSet->addLine($a[1], $a[2], $a[3], $a[4]);

                    $pl['x'] = $a[3];
                    $pl['y'] = $a[4];

                    $numRecords--;
                }

                else if ($numRecords &&
                         preg_match("#^([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})([ -][0-9]\.[0-9]{7}E[-+][0-9]{2})#", $line, $a)) {
                    if ($pl['x'] != -1 &&
                        $pl['y'] != -1) {
                        $lineSet->addLine($pl['x'], $pl['y'], $a[1], $a[2]);

                        $pl['x'] = $a[1];
                        $pl['y'] = $a[2];
                    }

                    $numRecords--;
                }

                else if ($ln > 2) {
                    if ($this->debug) {
                        printf(
                          'Died at: %s<br />',
                          $ln
                        );
                    }

                    break;
                }

                else if ($this->debug) {
                    echo $line . '<br />';
                }
            }

            @fclose($fp);

            return $lineSet;
        }

        return false;
    }
}
?>
