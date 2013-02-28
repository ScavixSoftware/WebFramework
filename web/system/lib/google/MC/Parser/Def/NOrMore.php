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

class MC_Parser_Def_NOrMore extends MC_Parser_Def {
    public $expr;
    public $min;
    
    public function __construct(MC_Parser_Def $expr, $min) {
        $this->expr = $expr;
        $this->min = (int) $min;
    }
    
    public function _parse($str, $loc) {
        $toks = $this->tokenGroup();
        try {
            while(true) {
                list($loc, $tok) = $this->expr->parsePart($str, $loc);
                $toks->append($tok);
            }
        } catch(MC_Parser_ParseError $e) {
            //Ignore parsing errors - that just means we're done
        }
        
        if($toks->count() < $this->min) {
            throw new MC_Parser_ParseError('Expected: ' . $min . ' or more ' . $expr->name, $str, $loc);
        }
        
        if($toks->count() == 0) {
            //If this token is empty, remove it from the result group
            $toks = null;
        }
        return array($loc, $toks);
    }
    
    public function _name() {
        return $this->min . ' or more: ' . $this->expr->getName();
    }
}

?>
