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
namespace ScavixWDF\Admin;

use ScavixWDF\Controls\Form\Form;

/**
 * @attribute[NoMinify]
 */
class MinifyAdmin extends SysAdmin
{
	/**
	 * @attribute[RequestParam('submitter','bool',false)]
	 * @attribute[RequestParam('skip_minify','bool',false)]
	 * @attribute[RequestParam('random_nc','bool',false)]
	 */
	function Start($submitter,$skip_minify,$random_nc)
	{
		global $CONFIG;
		
		if( !$submitter )
		{
			$this->_contentdiv->content("<h1>Select what to minify</h1>");
			$form = $this->_contentdiv->content( new Form() );
			$form->AddHidden('submitter','1');
			$form->AddCheckbox('skip_minify','Skip minify (only collect and combine)<br/>');
			$form->AddCheckbox('random_nc','Generate random name (instead of app version)<br/>');
			$form->AddSubmit('Go');
			return;
		}
		
		$this->_contentdiv->content("<h1>Minify DONE</h1>");
		$parts = array_diff($CONFIG['class_path']['order'], array('system','model','content'));
		$paths = array();
		foreach( $parts as $part )
			$paths = array_merge ($paths,$CONFIG['class_path'][$part]);
		
		sort($paths);
		$root_paths = array();
		foreach( $paths as $i=>$cp )
		{
			$root = true;
			for($j=0; $j<$i && $root; $j++)
				if(starts_with($cp, $paths[$j]) )
					$root = false;
			if( $root )
				$root_paths[] = $cp;
		}
		
		if( $skip_minify )
			$GLOBALS['nominify'] = '1';
		
		$target_path = cfg_get('minify','target_path');
		system_ensure_path_ending($target_path,true);
		$target = $target_path.cfg_get('minify','base_name');
		minify_all($root_paths, $target, $random_nc?md5(time()):getAppVersion('nc'));
	}
}