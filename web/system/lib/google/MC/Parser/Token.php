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

require_once 'MC/Parser/Token/Group.php';

/**
 * An instance of a parsed token
 */
class MC_Parser_Token {
    public $name = null;
    public $value = null;
    
    public function __construct($value, $name=null) {
        $this->value = $value;
        $this->name = $name;
    }
    
    public function getValues() {
        return array($this->value);
    }
    
    public function getNameValues() {
        return array(array($this->name, $this->value));
    }
    
    public function hasChildren() {
        return false;
    }
    
    public function getChildren() {
        return array();
    }
}

?>
