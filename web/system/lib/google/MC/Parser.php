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

require_once 'MC/Parser/Def.php';
require_once 'MC/Parser/Token.php';

class MC_Parser_Error extends Exception {}
class MC_Parser_DefError extends MC_Parser_Error {}
class MC_Parser_ParseError extends MC_Parser_Error {
    public $data;
    public $loc;
    
    public function __construct($msg, $str, $loc) {
        $this->data = $str;
        $this->loc = $loc;
        parent::__construct($msg);
    }
}

/**
 * Parser-generator class with an easy PHP-based API, similar to the pyparsing module in philosophy
 */
class MC_Parser {
    /**
     * By default, the parser ignores these characters when they occur between tokens
     * @var string
     */
    public static $whitespace = " \t\n\r";
    
    /**
     * Return a MC_Parser_Def_Set with the function arguments as the subexpressions
     * @return MC_Parser_Def_Set
     */
    public function set() {
        return new MC_Parser_Def_Set(func_get_args());
    }
    
    /**
     * Return a MC_Parser_Def_OneOf with the function arguments as the possible expressions
     * @return MC_Parser_Def_OneOf
     */
    public function oneOf() {
        return new MC_Parser_Def_OneOf(func_get_args());
    }
    
    /**
     * Return a MC_Parser_Def_Word that matches a set of possible characters not separated by whitespace
     * @param string $first_chars
     * @param string|null $rest_chars
     * @return MC_Parser_Def_Word
     */
    public function word($first_chars, $rest_chars=null) {
        return new MC_Parser_Def_Word($first_chars, $rest_chars);
    }
    
    /**
     * Return a MC_Parser_Def_Regex that matches a typical optionally-escaped quoted string
     * @param string $quote_chars
     * @param string $esc_char
     * @return MC_Parser_Def_Regex
     */
    public function quotedString($quote_chars='\'"', $esc_char='\\') {
        $quote_chars = trim($quote_chars);
        if(!$quote_chars) throw new MC_Parser_DefError('$quote_chars cannot be an empty string');
        if(strlen($esc_char) > 1) throw new MC_Parser_DefError('Only one $esc_char can be defined');
        
        $quote_chars = str_split($quote_chars);
        if($esc_char) $esc_char = preg_quote($esc_char);
        
        $tpl = '(?:Q(?:[^Q\n\rE]|(?:QQ)|(?:Ex[0-9a-fA-F]+)|(?:E.))*Q)';
        foreach($quote_chars as $quote) {
            $quote = preg_quote($quote);
            $pats[] = str_replace(array('Q', 'E'), array($quote, $esc_char), $tpl);
        }
        
        $regex = implode('|', $pats);
        return new MC_Parser_Def_Regex($regex, 'mus', 'quoted string');
    }
    
    /**
     * wrapper around MC_Parser_Def_Regex that matches numerical values (like 7, 3.5, and -42)
     * @return MC_Parser_Def_Regex
     */
    public function number() {
        return new MC_Parser_Def_Regex('[+\-]?\d+(\.\d+)?', null, 'number');
    }
    
    /**
     * wrapper around MC_Parser_Def_OneOf that matches true and false, depending on case requirements
     * @param string $case which case is supported, one of "upper", "lower", "first", or "mixed"
     * @return MC_Parser_Def_OneOf
     */
    public function boolean($case='mixed') {
        switch($case) {
            case 'lower':
                return $this->oneOf($this->keyword('true'), $this->keyword('false'));
            case 'upper':
                return $this->oneOf($this->keyword('TRUE'), $this->keyword('FALSE'));
            case 'first':
                return $this->oneOf($this->keyword('True'), $this->keyword('False'));
            case 'mixed':
                return $this->oneOf($this->keyword('true', true), $this->keyword('false', true));
            default:
                throw new MC_Parser_DefError('Boolean case must be one of "upper", "lower", "first" or "mixed" - got "' . $case . '"');
        }
    }
    
    /**
     * Returns a MC_Parser_Def_Literal that matches a literal word
     * @param string $str the exact string to match
     * @param boolean $caseless flag for triggering case-insensitive searching
     * @param boolean $fullword for triggering literals that can be followed by non-whitespace characters
     */
    public function literal($str, $caseless=false, $fullword=false) {
        return new MC_Parser_Def_Literal($str, $caseless, $fullword);
    }
    
    /**
     * Returns a MC_Parser_Def_Literal that matches a literal word (but with non-whitespace following characters turned off)
     * @param string $str the exact string to match
     * @param boolean $caseless flag for triggering case-insensitive searching
     * @param boolean $fullword for triggering literals that can be followed by non-whitespace characters
     */
    public function keyword($str, $caseless=false, $fullword=true) {
        return new MC_Parser_Def_Literal($str, $caseless, $fullword);
    }
    
    /**
     * Returns a MC_Parser_Def_Set that matches a set of expressions delimited by a literal and optional whitespace
     * @param MC_Parser_Def $expr the expression that is delimited
     * @param string $delim the delimiting literal - defaults to ,
     * @return MC_Parser_Def_Set
     */
    public function delimitedList(MC_Parser_Def $expr, $delim=',') {
        return $this->set($expr, $this->zeroOrMore($this->set($this->literal($delim, false, false)->suppress(), $expr)));
    }
    
    /**
     * Returns a MC_Parser_Def_NOrMore that matches zero or more expressions
     * @param MC_Parser_Def $expr the expression to match zero or more of
     * @return MC_Parser_Def_NOrMore
     */
    public function zeroOrMore(MC_Parser_Def $expr) {
        return new MC_Parser_Def_NOrMore($expr, 0);
    }

    /**
     * Returns a MC_Parser_Def_NOrMore that matches one or more expressions
     * @param MC_Parser_Def $expr the expression to match one or more of
     * @return MC_Parser_Def_NOrMore
     */
    public function oneOrMore(MC_Parser_Def $expr) {
        return new MC_Parser_Def_NOrMore($expr, 1);
    }
    
    /**
     * Returns a MC_Parser_Def_Recursive placeholder def that can be used to create recursive grammars
     * @return MC_Parser_Def_Recursive
     */
    public function recursive() {
        return new MC_Parser_Def_Recursive();
    }
    
    /**
     * Returns a MC_Parser_Def_OneOf that matches zero or one expressions
     * @return MC_Parser_Def_OneOf
     */
    public function optional(MC_Parser_Def $expr) {
        $empty = new MC_Parser_Def_Empty();
        return $this->oneOf($expr, $empty);
    }
    
    /**
     * Helper function returning a regex range of all the characters in the english alphabet
     * @return string
     */
    public function alphas() {
        return 'a-zA-Z';
    }
    
    /**
     * Helper function returning a string of all alphabet and digit characters
     * @return string
     */
    public function alphanums() {
        return $this->alphas() . $this->nums();
    }
    
    /**
     * Helper function returning a regex range of all digit characters
     * @return string
     */
    public function nums() {
        return '0-9';
    }
    
    /**
     * Simple test for whether a character is a whitespace character
     * @return boolean
     */
    public static function isWhitespace($test) {
        return (strpos(self::$whitespace, $test) !== false);
    }
}

?>
