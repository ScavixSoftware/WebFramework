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
 * Generic grammar rule for matching a regular expresion
 */
class MC_Parser_Def_Regex extends MC_Parser_Def {
    /**
     * Subclasses of this can just modify the $regex, $flags, and $errstr properties
     */
    public $regex = null;
    public $flags = 'u';
    public $errstr = null;
    public $retgroup = 0;
    
    public function __construct($regex=null, $flags=null, $errstr=null) {
        if($regex !== null) $this->regex = $regex;
        if($flags !== null) $this->flags = $flags;
        if($errstr !== null) $this->errstr = $errstr;
    }
    
    public function _parse($str, $loc) {
        preg_match('/^' . $this->regex . '/' . $this->flags, substr($str, $loc), $matches,  PREG_OFFSET_CAPTURE);
        $success = isset($matches[$this->retgroup])?$matches[$this->retgroup]:false;
        if(empty($success) || $success[1] != 0) {
            throw new MC_Parser_ParseError('Expected: ' . (($this->errstr) ? $this->errstr : 'matching ' . $this->regex), $str, $loc);
        }
        
        $loc += strlen($success[0]);
        
        return array($loc, $this->token($success[0]));
    }
    
    public function _name() {
        if($this->errstr) return $this->errstr;
        return 'matches: ' . $this->regex;
    }
}

?>
