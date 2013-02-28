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
 * Match a "word", with the allowed characters defined by the $first_chars and $rest_chars options
 */
class MC_Parser_Def_Word extends MC_Parser_Def_Regex {
    /**
     * @param string $first_chars the characters allowed as the first character in the word
     * @param string $rest_chars the characters allwed as the rest of the word - defaults to same as $first_chars
     */
    public function __construct($first_chars, $rest_chars=null) {
        if($rest_chars === null) $rest_chars = $first_chars;
        $this->first_chars = $first_chars;
        $this->rest_chars = $rest_chars;
        
        if($first_chars === $rest_chars) {
            $this->regex = '[' . ($first_chars) . ']+';
        } else {
            $this->regex = '[' . ($first_chars) . '][' . ($rest_chars) . ']*';
        }
        
    }
}

?>
