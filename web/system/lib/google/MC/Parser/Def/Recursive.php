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

class MC_Parser_Def_Recursive extends MC_Parser_Def {
    
    public $replacement = null;
    
    public function _parse($str, $loc) {
        if($this->replacement === null) {
            throw new MC_Parser_DefError('You must replace the recursive placeholder before parsing a grammar');
        }
        
        return $this->replacement->_parse($str, $loc);
    }
    
    /**
     * When actually parsing the grammar, use this rule to validate the recursive rule - this must be called before parsing begins
     * @param MC_Parser_Def $expr
     * @return MC_Parser_Recursive chainable method - returns $this
     */
    public function replace(MC_Parser_Def $expr) {
        $this->replacement = $expr;
        return $this;
    }
    
    public function _name() {
        if($this->replacement === null) return 'placeholder';
        return $this->replacement->getName();
    }
}

?>
