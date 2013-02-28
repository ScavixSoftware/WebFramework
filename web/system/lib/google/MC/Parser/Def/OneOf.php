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
 * Successfully match one of a set of potential expressions - the longest match wins if there are multiples
 */
class MC_Parser_Def_OneOf extends MC_Parser_Def {
    public $exprs = array();
    
    public function __construct($exprs=array()) {
        if(!is_array($exprs)) {
            throw new MC_Parser_DefError('alternative sub-expressions must be an array');
        }
        
        $this->exprs = $exprs;
    }
    
    /**
     * @param string $str the string to parse
     * @param integer $loc the index to start parsing
     * @return array
     */
    public function _parse($str, $loc) {
        $max_match = -1;
        $res = null;
        foreach($this->exprs as $expr) {
            try {
                list($loc2, $toks) = $expr->parsePart($str, $loc);
                if($loc2 > $max_match) {
                    $max_match = $loc2;
                    $res = $toks;
                }
            } catch(MC_Parser_ParseError $e) {
                //Ignore any non-matching conditions
            }
        }
        
        if($max_match == -1) {
            throw new MC_Parser_ParseError('No alternative options match', $str, $loc);
        }
        if($this->name && !$res->name) $res->name = $this->name;
        
        return array($max_match, $res);
    }
    
    public function _name() {
        $names = array();
        foreach($this->exprs as $expr) {
            $names[] = $expr->getName();
        }
        
        return 'one of (' . implode(', ', $names) . ')';
    }
}

?>
