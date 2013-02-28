<?
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>
 *
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

/**
 * Serializer/Unserializer
 * 
 * We have our very own that support some specialities like database reconnection, datetime formats, reflection,...
 * As we implemented our own object storage and serialize it in one run, we can be sure that
 * the referential integrity will be given.
 */
class Serializer
{
	var $Stack;
	var $Lines;
//	var $PassedLines;

	/**
	 * Serializes a value
	 * 
	 * Can be anything from complex object to bool value
	 * @param mixed $data Value to serialize
	 * @return string Serialized data string
	 */
	function Serialize(&$data)
	{
		$this->Stack = array();
		return $this->Ser_Inner($data);
	}
 
	private function Ser_Inner(&$data,$level=0)
	{
		if( is_string($data) )
		{
			return "s:". str_replace("\n","\\n",$data) ."\n";
		}
		elseif( is_int($data) )
		{
			return "i:$data\n";
		}
		elseif( is_array($data) )
		{
			$res = "a:".count($data)."\n";
			$keys = array_keys($data);
			foreach( $keys as &$key )
			{
				$res .= "k:".$this->Ser_Inner($key,$level+1);
				$res .= "v:".$this->Ser_Inner($data[$key],$level+1);
			}
			return $res;
		}
		elseif( is_object($data) )
		{
			if( $data instanceof DataSource )
				return "m:".$data->_storage_id."\n";
			if( $data instanceof ADORecordSet_mysql || $data instanceof PDOStatement || $data instanceof Closure )
				return "n:\n";
			if( $data instanceof DateTimeEx )
				return "x:".$data->format('c')."\n";
			if( $data instanceof DateTime )
				return "d:".$data->format('c')."\n";
			if( $data instanceof Reflector )
				return "y:".$data->getName()."\n";

			foreach( $this->Stack as $index=>&$val )
				if( equals($this->Stack[$index], $data) )
					return "r:$index\n";

			$this->Stack[] = $data;
			$id = count($this->Stack) - 1;

			if( system_method_exists($data, '__sleep') )
				$vars = $data->__sleep();
			else
				$vars = array_keys(get_object_vars($data));

            $max = count($vars);

			if( $data instanceof Model)
				$res = "o:$id:".$max.":".get_class($data).":{$data->DataSourceName()}\n";
			else
				$res = "o:$id:".$max.":".get_class($data).":\n";
			
            $i = 0;
//			while($i++<$max)
			foreach( $vars as $field )
			{
//                $field = $vars[$i];
				$res .= "f:".$this->Ser_Inner($field,$level+1);
				$res .= "v:".$this->Ser_Inner($data->$field,$level+1);
			}

			return $res;
		}
		elseif( is_bool($data) )
		{
			return "b:".($data?'1':'0')."\n";
		}
		elseif( is_float($data) )
		{
			return "f:$data\n";
		}
		elseif( empty($data) )
		{
			return "n:\n";
		}
	}

	/**
	 * Restores something from a serialized data string
	 * 
	 * Note that of course all types used in that string must be known to the unserializing application!
	 * @param string $data Serialized data
	 * @return mixed Whatever was serialized
	 */
	function Unserialize($data)
	{
//		log_debug("Unserialize(...)",$data);
		if( !isset($GLOBALS['unserializing_level']) )
			$GLOBALS['unserializing_level'] = 0;
		$GLOBALS['unserializing_level']++;
		$this->Lines = explode("\n",trim($data));
		$this->Stack = array();
		$res = $this->Unser_Inner();
		$GLOBALS['unserializing_level']--;
		return $res;
	}

	private function Unser_Inner()
	{
		$orig_line = array_shift($this->Lines);
		if( $orig_line == "" )
			return null;
		$type = $orig_line{0};
		$line = substr($orig_line, 2);

		if( $type == 'k' || $type == 'f' || $type == 'v')
		{
			$type = $line{0};
			$line = substr($line, 2);
		}

		switch( $type )
		{
			case 's':
				return str_replace("\\n","\n",$line);
			case 'i':
				return intval($line);
			case 'a':
				$res = array();
				for($i=0; $i<$line; $i++)
				{
					$key = $this->Unser_Inner();
					$res[$key] = $this->Unser_Inner();
				}
				return $res;
			case 'd':
				return new DateTime($line);
			case 'x':
				return new DateTimeEx($line);
			case 'y':
				return new System_Reflector($line);
			case 'o':
				list($id,$len,$type,$datasource) = explode(':',$line);
				$datasource = $datasource?model_datasource($datasource):null;

				try{
					$this->Stack[$id] = new $type($datasource);
					for($i=0; $i<$len; $i++)
					{
						$field = $this->Unser_Inner();
						if( $field == "" )
							continue;

						$this->Stack[$id]->$field = $this->Unser_Inner();
					}

					if( system_method_exists($this->Stack[$id],'__wakeup') )
						$this->Stack[$id]->__wakeup();
					
				}catch(Exception $ex){
					WdfException::Log("Unserialise Exception in line '$line' ($id,$len,$type,$datasource)",$ex);
					return null;
				}
				return $this->Stack[$id];
				
			case 'r':
				if( !isset($this->Stack[intval($line)]) )
					WdfException::Raise("Trying to reference unknown object.");
				if( $this->Stack[intval($line)] instanceof DataSource )
					return model_datasource($this->Stack[intval($line)]->_storage_id);
				return $this->Stack[intval($line)];
			case 'm':
				return model_datasource($line);
			case 'n':
				return null;
			case 'f':
				return floatval($line);
			case 'b':
				return $line==1;
			default:
				WdfException::Raise("Unserialize found unknown datatype '$type'. Line was $orig_line");
		}
	}
}
