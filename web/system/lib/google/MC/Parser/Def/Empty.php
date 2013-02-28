<?php
/**
 * Parser Generator for PHP
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 *
 * @package    MC_Parser
 * @author     Chadwick Morris <chad@mailchimp.com>
 * @license    http://www.opensource.org/licenses/mit-license.php
 * @version    0.1
 */

/**
 * This match always succeeds with a zero-length suppressed token - useful for doing any kind of optional matching
 */
class MC_Parser_Def_Empty extends MC_Parser_Def {
    
    public $suppress = true;
    
    public function _parse($str, $loc) {
        return array($loc, $this->token(null));
    }
    
    public function _name() {
        return 'empty string';
    }
}

?>
