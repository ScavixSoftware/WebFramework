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
 * Verify that the string matches a series of subexpressions in the specified order
 */
class MC_Parser_Def_Set extends MC_Parser_Def {
    public $exprs = array();
    
    public function __construct($exprs=array()) {
        if(!is_array($exprs)) {
            throw new MC_Parser_DefError('Set sub-expressions must be an array');
        }
        
        $this->exprs = $exprs;
    }
    
    /**
     * @param string $str the string to parse
     * @param integer $loc the index to start parsing
     * @return array
     */
    public function _parse($str, $loc) {
        $res = $this->tokenGroup();
        foreach($this->exprs as $expr) {
            list($loc, $toks) = $expr->parsePart($str, $loc);
            if(!empty($toks)) {
                $res->append($toks);
            }
        }
        
        return array($loc, $res);
    }
    
    public function _name() {
        $names = array();
        foreach($this->exprs as $expr) {
            $names[] = $expr->getName();
        }
        
        return '[' . implode(', ', $names) . ']';
    }
}

?>
