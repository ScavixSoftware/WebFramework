<?
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
default_string('TITLE_WARNING', 'Warning');

/**
 * Displays a uiDialog with an OK button.
 * 
 */
class uiMessageBox extends uiDialog
{
	/**
	 * @param string $message_text Message text
	 * @param string $type Defines the css class, so you may use this to style different kinds of messages
	 * @param string $title A title
	 */
    function __initialize($message_text,$type='hint',$title='TITLE_WARNING')
	{
		$options = array(
			'autoOpen'=>true,
			'modal'=>true,
			'width'=>450,
			'height'=>300

		);
		parent::__initialize($title,$options);
		$this->class = $type;

		$this->content($message_text);
		$this->AddCloseButton(tds('BTN_OK','Ok'));
	}
}
