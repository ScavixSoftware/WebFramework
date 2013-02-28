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

class MC_Parser_Def_Literal extends MC_Parser_Def {
    public $search = null;
    public $caseless = false;
    public $fullword = true;
    
    /**
     * Match against an exact set of characters in the string
     * @param string $str the search string
     * @param boolean $caseless set to true to ignore case
     * @param boolean $fullword set to false to allow a literal followed by a non-whitespace character
     */
    public function __construct($str, $caseless=false, $fullword=true) {
        $this->search = $str;
        $this->caseless = $caseless;
        $this->fullword = $fullword;
    }
    
    public function _parse($str, $loc) {
        if(!$this->caseless) {
            $match = strpos($str, $this->search, $loc);
        } else {
            $match = stripos($str, $this->search, $loc);
        }
        
        if($match !== $loc) {
            throw new MC_Parser_ParseError('Expected: ' . $this->search, $str, $loc);
        }
        
        $loc += strlen($this->search);
        
        if($this->fullword && $loc < strlen($str) && !MC_Parser::isWhitespace($str[$loc])) {
            throw new MC_Parser_ParseError('Expected: ' . $this->search, $str, $loc);
        }
        
        return array($loc, $this->token($this->search));
    }
    
    public function _name() {
        return $this->search;
    }
}

?>
