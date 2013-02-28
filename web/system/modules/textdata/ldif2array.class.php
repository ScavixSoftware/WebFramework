<?php
/**
* Class to read LDIF data
* @author tobozo <tobozo at phpsecure dot info>
* @contributor Vladimir Struchkov <great_boba at yahoo dot com>
* @contributor Wojciech Sznapka <wojciech at sznapka dot pl>
* @copyleft (l) 2006-2009  tobozo
* @package ldif2Array
* @version 1.3
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-12-13
*/

class ldif2array {

    /**
    * stores file name
    * @type string
    */
    var $file;

    /**
    * store data
    * @type string
    */
    var $rawdata;

    /**
    * store entries
    * @type array
    */
    var $entries = array();


    //== constructor ====================================================================
    function ldif2array(/*string*/$file='', /*bool*/$process=false) {
      $this->file = $file;
      if($process) {
        $this->makeArray();
      }
    }


    /**
    * returns the array of LDIF entries
    * @return array
    */
    function getArray() {
      return $this->entries;
    }


    /**
    * Sanity check before building the array, returns false if error
    * @return bool
    */
    function makeArray() {
       if($this->file=='') {
         if($this->rawdata=='') {
           echo "No filename specified, aborting";
           return false;
         }
       } else {
         if(!file_exists($this->file)) {
           echo "File $this->file does not exist, aborting";
           return false;
         } else {
           $this->rawdata  = file_get_contents($this->file);
         }
       }

       if($this->rawdata=='') {
         echo "No data in file, aborting";
         return false;
       }

       $this->parse2array();
       return true;

       if(!$this->splitEntries()) {
         echo "Could not extract data from this file, aborting";
         return false;
       }
       $this->splitBlocks();
       sort($this->entries);
       return true;
    }

	private function _setEntryValue($k1,$i,$value)
	{
		if (isset($this->entries[$k1][$i]))
		{
			if (!is_array($this->entries[$k1][$i]))
			{
				$this->entries[$k1][$i]=array($this->entries[$k1][$i]);
			}
			$this->entries[$k1][$i][]=trim($value);
		}
		else
		{
			$this->entries[$k1][$i]=trim($value);
		}
	}

    /**
    * Build the array in two passes
    * @return void
    */
    function parse2array()  {
        /**
        * Thanks to Vladimir Struchkov <great_boba yahoo com> for providing the
        * code to extract base64 encoded values
        */

        $arr1=explode("\n",str_replace("\r",'',$this->rawdata));
        $i=$j=0;
        $arr2=array();

        /* First pass, rawdata is splitted into raw blocks */
        foreach($arr1 as $v)  {
            if (trim($v)=='') {
                ++$i;
                $j=0;
            } else {
                $arr2[$i][$j++]=trim($v);
            }
        }

        /* Second pass, raw blocks are updated with their name/value pairs */
        foreach($arr2 as $k1=>$v1) {
            $i=0;
            $decode=false;
            foreach($v1 as $v2) {
                if (strpos($v2,'::')!==false) { // base64 encoded, chunk start
                    $decode=true;
                    $arr=explode(':',str_replace('::',':',$v2));
                    $i=$arr[0];
					$this->_setEntryValue($k1,$i,base64_decode($arr[1]));
                } elseif (strpos($v2,':')!==false) {
                    $decode=false;
                    $arr=explode(':',$v2);
                    $count=count($arr);
                    if ($count!=2)
                        for($i=$count-1;$i>1;--$i)
                            $arr[$i-1].=':'.$arr[$i];
                    $i=$arr[0];

                    $this->_setEntryValue($k1,$i,$arr[1]);
                } else {
                    if ($decode) { // base64 encoded, next chunk
						$this->_setEntryValue($k1,$i,$this->entries[$k1][$i].base64_decode($v2) );
                    } else {
						$this->_setEntryValue($k1,$i,trim($v2));
                    }
                }
            }
        }
    }



}; // end class
