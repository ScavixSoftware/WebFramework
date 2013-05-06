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

class Blog extends HtmlPage
{
	function __initialize()
	{
		parent::__initialize();
		$ds = model_datasource('system');
		$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS blog(id INTEGER,title VARCHAR(50),body TEXT,PRIMARY KEY(id))");
		
		$this->content(new Anchor( buildQuery('blog','newpost'), 'New post' ));
	}
	
	function Index()
	{
		$ds = model_datasource('system');
		foreach( $ds->Query('blog')->orderBy('id','desc') as $post )
		{
			$tpl = Template::Make('post')
				->set('title',$post->title)
				->set('body',$post->body);
			$this->content($tpl);
		}
	}
	
	function NewPost()
	{
		log_debug("New Post");
		$this->content( Template::Make('newpostform') );
	}
	
	/**
	 * @attribute[RequestParam('title','string')]
	 * @attribute[RequestParam('body','string')]
	 */
	function AddPost($title,$body)
	{
		log_debug("Add Post");
		$ds = model_datasource('system');
		$ds->ExecuteSql("INSERT INTO blog(title,body)VALUES(?,?)",array($title,$body));
		redirect('blog','index');
	}
}