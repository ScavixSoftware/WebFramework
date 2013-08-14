<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) since 2012 Scavix Software Ltd. & Co. KG
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
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Base;

use stdClass;
use ScavixWDF\WdfException;

/**
 * Helper class to easily create standard AJAX responses.
 */
class AjaxResponse
{
	var $_data = false;
	var $_text = false;
	private $_translated = false;
	
	/**
	 * This is a valid Noop return.
	 * 
	 * @return AjaxResponse The created response
	 */
	public static function None()
	{
		return new AjaxResponse();
	}
	
	/**
	 * Return just script code to be executed clientside immetiately.
	 * 
	 * If $abort_handling is true the clientside success/error handling will not be polled
	 * @param mixed $script JS code as string or array
	 * @param bool $abort_handling Abort clientside success/error handling
	 * @return AjaxResponse The created response
	 */
	public static function Js($script=false,$abort_handling=false)
	{
		$data = new stdClass();
		$data->script = force_array($script);
		$data->abort = $abort_handling;
		return AjaxResponse::Json($data);
	}
	
	/**
	 * Return data JSON formatted ($data can be anything!).
	 * 
	 * @param mixed $data Data to be passed out
	 * @return AjaxResponse The created response
	 */
	public static function Json($data=null)
	{
		$res = new AjaxResponse();
		if( $data !== null )
			$res->_data = $data;
		return $res;
	}
	
	/**
	 * Return a plain text.
	 * 
	 * @param string $text Text to be passed out
	 * @return AjaxResponse The created response
	 */
	public static function Text($text=false)
	{
		$res = new AjaxResponse();
		if( $text !== false )
			$res->_text = $text;
		return $res;
	}
	
	/**
	 * Return a Controller (with full init-code).
	 * 
	 * @param Renderable $content Content to be passed out
	 * @return AjaxResponse The created response
	 */
	public static function Renderable(Renderable $content)
	{
		$wrapped = new stdClass();

		$wrapped->html = $content->WdfRenderAsRoot();
		if( $content->_translate && system_is_module_loaded('translation') )
			$wrapped->html = __translate($wrapped->html);
		
		foreach( $content->__collectResources() as $r )
		{
			if( starts_with(pathinfo($r,PATHINFO_EXTENSION), 'css') )
				$wrapped->dep_css[] = $r;
			else
				$wrapped->dep_js[] = $r;
		}
		$res = AjaxResponse::Json($wrapped);
		$res->_translated = true;
		return $res;
	}
	
	/**
	 * Return an error.
	 * 
	 * If $abort_handling is true the clietside error handling will not be polled
	 * @param string $message The error message
	 * @param bool $abort_handling Abort clientside success/error handling
	 * @return AjaxResponse The created response
	 */
	public static function Error($message,$abort_handling=false)
	{
		$data = new stdClass();
		$data->error = $message;
		$data->abort = $abort_handling;
		return AjaxResponse::Json($data);
	}
	
	/**
	 * Let the client redirect.
	 * 
	 * @param mixed $controller The controller to be loaded (can be <Renderable> or string)
	 * @param string $event The event to be executed
	 * @param mixed $data Optional data to be passed (string or array)
	 * @return AjaxResponse The created response
	 */
	public static function Redirect($controller,$event='',$data='')
	{
		$q = buildQuery($controller,$event,$data);
		return AjaxResponse::Js("wdf.redirect('$q');");
	}
	
	/**
	 * @internal Renders the response for output.
	 */
	function Render()
	{
		if( $this->_data )
		{
			if( isset($this->_data->script) )
				$this->_data->script = "<script>".implode("\n",$this->_data->script)."</script>";
			$res = system_to_json($this->_data);
		}
		elseif( $this->_text )
			$res = json_encode($this->_text);
		else
			return '""'; // return an empty string JSON encoded to not kill the app JS side
		return !$this->_translated&&system_is_module_loaded("translation")?__translate($res):$res;
	}
	
	/**
	 * Allows addition of scripts to responses.
	 * 
	 * Sometimes it is useful to add JS codes for immediate execution. In plain HTML request this
	 * would be done with for example <Control::script>() which adds the script to the parent pages init method,
	 * but for AJAX requests we need to go this way.
	 * @param string|array $script script code or array of script codes to be added.
	 * @return void
	 */
	function AddScript($script)
	{
		if( !$this->_data )
			WdfException::Raise("Cannot add script code to AJAX response of type text");
		
		if( !isset($this->_data->script) )
			$this->_data->script = force_array($script);
		else
			$this->_data->script = array_merge($this->_data->script,force_array($script));
	}
}
